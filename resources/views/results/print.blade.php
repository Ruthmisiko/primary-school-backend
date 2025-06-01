<!DOCTYPE html>
<html>
<head>
    <title>Student Results</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
        th { background: #f0f0f0; }
        h2{
            text-align: center;
            color: crimson;
        }
    </style>
</head>
<body>
@php
    $exam = $student->results->first()->exam ?? null;
@endphp

<h2>SMART SCHOOL</h2>
<h2>Student Results</h2>
@if ($exam)
    <h3>EXAM: {{ $exam->name }} ,  {{$exam->year}}</h3>
    <h3>TERM: {{ $exam->term }}</h3>
    <h3>TEACHER: {{ $student->sclass->teacher->name ?? 'N/A' }}</h3>


@else
    <h3>EXAM: N/A</h3>
    <h3>YEAR: N/A</h3>
    <h3>TERM: N/A</h3>
@endif
    <h3><strong>STUDENTS NAME:</strong> {{ $student->name }}</h3>
    <!-- <p><strong>Class:</strong> {{ $student->sclass->name ?? 'N/A' }}</p> -->

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Subject</th>
                <th>Marks</th>
                <th>Grade</th>
            </tr>
        </thead>
        <tbody>
            @foreach($student->results as $index => $result)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $result->subject->name ?? 'N/A' }}</td>
                <td>{{ $result->marks_obtained }}</td>
                <td>{{ $result->grade }}</td>
            </tr>
            @endforeach
        </tbody>

    </table>
    <h3 style="margin-top: 5%;">TOTAL MARKS: {{ $student->results->sum('marks_obtained') }}</h3>


</body>
</html>
