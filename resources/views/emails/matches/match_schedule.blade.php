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
<h1>Hello, Team</h1>
<b> <p>Your team has been selected for the match schedule in the tournament !</p> </b>
<p>Tournament Name:</p>
<p>Start Date:</p>
<p>End Date: </p>
<p>Location: </p>
<p><b>Team Name:</b> </p>
<p><b>Team Coach:</b> </p>
<p><b>Phone Number:</b> </p>
<div class="table_container">
    <div class="team_table_container">

    </div>
</div>

<div class="signature_container">
    <div>Kind regards,</div>
    <div>SEMS Organization</div>
</div>

</body>

</html>
