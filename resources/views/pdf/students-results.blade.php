<!DOCTYPE html>
<html>
<head>
    <title>Student Results</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
        th { background: #f0f0f0; }
    </style>
</head>
<body>
    <h2>Student Results</h2>
    <p><strong>Name:</strong> {{ $student->name }}</p>
    <p><strong>Class:</strong> {{ $student->sclass->name ?? 'N/A' }}</p>

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
</body>
</html>
