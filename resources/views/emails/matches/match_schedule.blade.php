<!DOCTYPE html>
<html>

<head>
    <title>Match Schedule Notification</title>
</head>
<style>
    .image_container {
        display: flex;
        justify-content: center;
    }

    .signature_container {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .table_container {
        display: flex;
    }

    .team_table_container table,
    .team_table_container table th,
    .team_table_container table td {
        border: 1px solid black;
        border-collapse: collapse;
        padding: 6px 20px;
        text-align: left;
    }

    .team_table_container table th {
        background-color: #6c7ae0;
        color: white;
    }
</style>

<body>
<div class="image_container">
    <img src="{{ url('uploads/logo/sems_logo.png') }}" alt="Sems_logo">
</div>
<h1>Hello, {{ $mailData['team']->team_name }} Team</h1>
<b> <p>Your team has been selected for the match schedule in the tournament {{ $mailData['tournament']->t_name }}!</p> </b>
<p>Tournament Name: {{ $mailData['tournament']->t_name }}</p>
<p>Start Date: {{ $mailData['tournament']->ts_date }}</p>
<p>End Date: {{ $mailData['tournament']->te_date }}</p>
<p>Location: {{ $mailData['tournament']->address }}</p>
<p><b>Team Name:</b> {{ $mailData['team']->team_name }}</p>
<p><b>Team Coach:</b> {{ $mailData['team']->coach_name }}</p>
<p><b>Phone Number:</b> {{ $mailData['team']->phone_number }}</p>
<div class="table_container">
    <div class="team_table_container">
        <table>
            <thead>
            <tr>
                <th>Match Date</th>
                <th>Opponent Team</th>
                <th>Location</th>
            </tr>
            </thead>
            <tbody>
            @foreach( $mailData['matches'] as $match )
            <tr>
                <td>{{ $match->date }}</td>
                <td>{{ $match->opponent_team }}</td>
                <td>{{ $match->location }}</td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="signature_container">
    <div>Kind regards,</div>
    <div>SEMS Organization</div>
</div>

</body>

</html>
