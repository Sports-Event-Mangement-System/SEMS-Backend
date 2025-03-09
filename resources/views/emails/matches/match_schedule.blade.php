<!DOCTYPE html>
<html>

<head>
    <title>Match Schedule Notification</title>
    <!-- Link to Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<style>
    body {
        font-family: 'Poppins', sans-serif;
    }

    .container {
        display: flex;
        flex-direction: column;
        gap: 30px;
        line-height: 1.6;
    }



    .match {
        display: flex;
        justify-content: center;
        gap: 6vh;
        margin-top: 20px;
    }

    .match_details {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .match_schedule {
        width: 18vh;
        display: flex;
    }

    .image_container {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        line-height: 1px;
    }

    .logo {
        height: 11vh;
        width: 13vh;
    }

    .system_logo {
        height: 15vh;
        width: 17vh;
    }

    .button {
        padding: 6px 10px;
        border-radius: 14px;
        border: 2px solid #ff7442;
        display: flex;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        background-color: #ff7442;
        font-weight: 600;
        color: white;
        transition: background-color 0.3s ease;
        margin-top: 10px;
    }

    .button:hover {
        background-color: #e86332;
    }
</style>

<body>
    <div class="container">
        <div class="image_container">
            <img class="system_logo" src="{{ url('uploads/logo/sems_logo.png') }}" alt="Sems_logo">
            <h2>Sports Event Management System</h2>
        </div>
        <div>
            @if($mailData['recipientType'] === 'team')
                <p>Important notification regarding your team's upcoming match in the {{ $mailData['tournament']->t_name }}!
                </p>
                <p>Your team, {{ $mailData['supportedTeam']->team_name }}, has been scheduled to compete against {{ $mailData['opponentTeam']->team_name }}.
                    As team management, please ensure all your players are informed and prepared for this important fixture.
                </p>

            @elseif($mailData['recipientType'] === 'player')
                <p>Get ready for your upcoming match in the {{ $mailData['tournament']->t_name }}!</p>
                <p>Your team, {{ $mailData['supportedTeam']->team_name }}, will be facing {{ $mailData['opponentTeam']->team_name }}.
                    As a valued player, your participation and preparation are crucial for the team's success.</p>

            @else
                <p>Thanks for following {{ $mailData['supportedTeam']->team_name }}!</p>
                <p>We're excited to inform you that {{ $mailData['supportedTeam']->team_name }} has an upcoming match against
                    {{ $mailData['opponentTeam']->team_name }}
                    in the {{ $mailData['tournament']->t_name }}.</p>
            @endif
        </div>
        <div class="match_details">
            <h1>KICKOFF MATCH</h1>
            <div class="match">
                <div class="image_container">
                    <img class="logo" src="{{ $mailData['participants'][0]['teamLogo'] ?? '' }}" alt="Sems_logo">
                    <h3>{{ $mailData['participants'][0]['name'] }}</h3>
                </div>
                <h2 class="match_schedule">
                    {{ $mailData['match']->startTime ?? 'Still to be scheduled' }}
                </h2>
                <div class="image_container">
                    <img class="logo" src="{{ url($mailData['participants'][1]['teamLogo'] ?? '') }}" alt="Sems_logo">
                    <h3>{{ $mailData['participants'][1]['name'] }}</h3>
                </div>
            </div>
            <button class="button">View Fixtures</button>
        </div>
        <div>
            <h1>WHAT CAN YOU EXPECT FROM OUR DEDICATED COVERAGE ?</h1>
            <button class="button">Stay Up To Date</button>
        </div>

    </div>
</body>

</html>
