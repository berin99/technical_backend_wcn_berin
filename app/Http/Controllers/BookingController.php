<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Classroom;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    public function getAvailableClasses()
    {
        $classrooms = Classroom::all();
        $availableClasses = [];
        $daysOfWeek = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

        foreach ($classrooms as $classroom) {
            $timetable = json_decode($classroom->timetable, true);

            foreach ($timetable as $day => $times) {
                $dayIndex = array_search($day, $daysOfWeek);
                $currentDate = Carbon::now()->startOfWeek()->addDays($dayIndex);

                foreach ($times as $time) {
                    $availableClasses[] = [
                        'classroom' => $classroom->name,
                        'day' => $currentDate->format('l'),
                        'date' => $currentDate->format('Y-m-d'),
                        'time' => $time . ':00',
                    ];
                }
            }
        }

        return response()->json($availableClasses);
    }

    public function bookClass(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'classroom_name' => 'required|string|exists:classrooms,name',
            'date' => 'required|date_format:Y-m-d',
            'time' => 'required|date_format:H:i',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }

        // Retrieve the classroom
        $classroom = Classroom::where('name', $request->classroom_name)->first();

        // Create start and end times
        $start_time = Carbon::createFromFormat('Y-m-d H:i', $request->date . ' ' . $request->time);
        $end_time = $start_time->copy()->addHour(); // Adjust based on booking interval

        // Check if the booking is within the current week
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();
        if (!$start_time->between($startOfWeek, $endOfWeek)) {
            return response()->json(['error' => 'Bookings can only be made for the current week'], 400);
        }

        // Retrieve the classroom timetable
        $timetable = json_decode($classroom->timetable, true);
        $dayOfWeek = strtolower($start_time->format('l'));

        if (!isset($timetable[$dayOfWeek])) {
            return response()->json(['error' => 'The requested slot is not available'], 400);
        }

        // Validate if the slot is available in the timetable
        $availableTimes = $timetable[$dayOfWeek];
        $requestedHour = intval($start_time->format('H'));
        $isValidTime = false;

        foreach ($availableTimes as $time) {
            if ($requestedHour == $time) {
                $isValidTime = true;
                break;
            }
        }

        if (!$isValidTime) {
            return response()->json(['error' => 'The requested slot is not available'], 400);
        }

        // Check for existing bookings that overlap with the requested time slot
        $existingBooking = Booking::where('classroom_id', $classroom->id)
            ->where(function($query) use ($start_time, $end_time) {
                $query->where(function($q) use ($start_time, $end_time) {
                    $q->where('start_time', '<', $end_time)
                    ->where('end_time', '>', $start_time);
                });
            })
            ->exists();

        if ($existingBooking) {
            return response()->json(['error' => 'Time slot is already booked'], 400);
        }

        // Create the booking
        Booking::create([
            'classroom_id' => $classroom->id,
            'start_time' => $start_time,
            'end_time' => $end_time,
        ]);

        return response()->json(['message' => 'Class booked successfully']);
    }

    public function cancelBooking(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
        ]);

        $booking = Booking::find($request->booking_id);
        $timeDifference = Carbon::now()->diffInHours($booking->start_time);

        if ($timeDifference < 24) {
            return response()->json(['error' => 'Cannot cancel booking less than 24 hours in advance'], 400);
        }

        $booking->delete();

        return response()->json(['message' => 'Booking canceled successfully']);
    }

    public function getClassBookings(Request $request)
    {
        $request->validate([
            'classroom_name' => 'required|string|exists:classrooms,name',
        ]);

        $classroom = Classroom::where('name', $request->classroom_name)->first();
        $bookings = Booking::where('classroom_id', $classroom->id)->get();

        return response()->json($bookings);
    }
}
