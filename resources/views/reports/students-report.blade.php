<!DOCTYPE html>
<html>
<head>
    <title>Teachers</title>
    <style>
        table {
            width: 100%;
            font-family: sans-serif;
            border-collapse: collapse;
        }

        tbody {
            background-color:#ffffff;
        }
        td{
            text-align: left;
            border: 1px solid #393d38;
            font-size: 0.8rem;
        }
        th{
            text-align: left;
        }
        .thead-list-header tr th{
            padding:1px;
            border: 1px solid #007ACC;
            background-color: #007ACC;
            color: #ffffff;
            color: #ffffff;


        }
        /* .thead-list-body tr td{
            font-size: 0.6rem;
        } */
        .table-letter-header tr th{
            padding:2px;
            font-size: 0.8rem;
        }
        tfoot{
            padding:2px;
            font-size: 0.5rem;
        }
        .report-title {
            text-align: center;
            font-size: 1.7rem;
            margin-bottom: 20px;
            font-family: sans-serif;
        }
        .head {
            text-align: center;
            font-family: sans-serif;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="head">
         <h2>Students Report</h2>
    </div>
    <table class="table table-letter-header">
        <tbody>
        <tr style="margin-bottom: 10px;">
            <th align="left">

            </th>
            <th style="font-weight: normal"></th>
        </tr>
        </tbody>
    </table>

<table class="table">
    <thead class="thead-list-header">
    <tr>
        <th width="5%">S/NO</th>
        <th width="11%">STUDENTS NAME</th>
        <th width="11%">PARENT</th>
        <th width="11%">CLASS</th>
        <th width="11%">AGE</th>
        <th width="11%">CREATED AT</th>
    </tr>
    </thead>
    <tbody class="thead-list-body">
    @foreach($students as $student)
        <tr>
        <td>{{ $loop->iteration }}</td>
            <td>{{ $student->name }}</td>
            <td>{{ $student->parent }}</td>
            <td>{{ $student->sclass->name}}</td>
            <td>{{ $student->age}}</td>
            <td>{{ $student->created_at->format('Y-m-d') }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>