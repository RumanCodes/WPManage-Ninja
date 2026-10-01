<?php
add_filter('fluent_booking/booked_events', function ($bookedEvents, $calendarSlot, $toTimeZone, $dateRange, $isDoingBooking) {

    $protectedEventId = 3;           // Group Coaching event ID
    $protectedDays    = [2, 4];      // 2 = Tuesday, 4 = Thursday
    $protectedStart   = '13:30:00';
    $protectedEnd     = '14:30:00';

    if (!$calendarSlot || empty($calendarSlot->id)) {
        return $bookedEvents;
    }

    // Never block the protected Group Coaching event itself.
    if ((int) $calendarSlot->id === (int) $protectedEventId) {
        return $bookedEvents;
    }

    $protectedEvent = \FluentBooking\App\Models\CalendarSlot::find($protectedEventId);

    if (!$protectedEvent) {
        return $bookedEvents;
    }

    // Only block other events under the SAME FluentBooking calendar/host.
    if ((int) $calendarSlot->calendar_id !== (int) $protectedEvent->calendar_id) {
        return $bookedEvents;
    }

    $scheduleTimezone = $protectedEvent->getScheduleTimezone();
    if (!$scheduleTimezone) {
        $scheduleTimezone = 'UTC';
    }

    try {
        $rangeStartLocal = \FluentBooking\App\Services\DateTimeHelper::convertToTimeZone($dateRange[0], 'UTC', $scheduleTimezone);
        $rangeEndLocal   = \FluentBooking\App\Services\DateTimeHelper::convertToTimeZone($dateRange[1], 'UTC', $scheduleTimezone);

        $startDate = new \DateTime(gmdate('Y-m-d', strtotime($rangeStartLocal)));
        $endDate   = new \DateTime(gmdate('Y-m-d', strtotime($rangeEndLocal)));
        $endDate->modify('+1 day');   // include the final day

        $period = new \DatePeriod($startDate, new \DateInterval('P1D'), $endDate);

        foreach ($period as $date) {
            $dayNumber = (int) $date->format('N');   // 1 = Mon ... 7 = Sun
            if (!in_array($dayNumber, $protectedDays, true)) {
                continue;
            }

            $localDate  = $date->format('Y-m-d');
            $localStart = $localDate . ' ' . $protectedStart;
            $localEnd   = $localDate . ' ' . $protectedEnd;

            $utcStart = \FluentBooking\App\Services\DateTimeHelper::convertToUtc($localStart, $scheduleTimezone);
            $utcEnd   = \FluentBooking\App\Services\DateTimeHelper::convertToUtc($localEnd, $scheduleTimezone);

            if ($toTimeZone && $toTimeZone !== 'UTC') {
                $outputStart = \FluentBooking\App\Services\DateTimeHelper::convertToTimeZone($utcStart, 'UTC', $toTimeZone);
                $outputEnd   = \FluentBooking\App\Services\DateTimeHelper::convertToTimeZone($utcEnd, 'UTC', $toTimeZone);
            } else {
                $outputStart = $utcStart;
                $outputEnd   = $utcEnd;
            }

            $dateKey = gmdate('Y-m-d', strtotime($outputStart));

            if (!isset($bookedEvents[$dateKey])) {
                $bookedEvents[$dateKey] = [];
            }

            $bookedEvents[$dateKey][] = [
                'event_id'  => null,
                'start'     => $outputStart,
                'end'       => $outputEnd,
                'remaining' => 0,
                'source'    => null
            ];
        }
    } catch (\Throwable $e) {
        return $bookedEvents;
    }

    return $bookedEvents;
}, 20, 5);