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
            <p>Hello Subash!</p>
            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Laborum, veniam. Rerum, repudiandae! Omnis nostrum suscipit cupiditate adipisci tempore, praesentium aliquam! Lorem ipsum dolor, sit amet consectetur adipisicing elit. Eius ipsum nostrum, iure voluptas, quae vitae aut quidem eveniet omnis ab similique quod. Id, at deleniti omnis totam similique voluptatum labore ducimus fuga exercitationem nemo expedita aperiam hic incidunt fugit officia nesciunt! Consequuntur pariatur facilis, accusantium consectetur ipsum nam. Aliquid, ab?
            </p>
        </div>
        <div class="match_details">
            <h1>KICKOFF MATCH</h1>
            <div class="match">
                <div class="image_container">
                    <img class="logo" src="{{ url('uploads/logo/sems_logo.png') }}" alt="Sems_logo">
                    <h3>Subash</h3>
                </div>
                <h2 class="match_schedule">
                    Sat 22 July 10:30 AM
                </h2>
                <div class="image_container">
                    <img class="logo" src="{{ url('uploads/logo/sems_logo.png') }}" alt="Sems_logo">
                    <h3>Pratik</h3>
                </div>
            </div>
            <button class="button">View Fixtures</button>
        </div>
        <div>
            <h1>WHAT CAN YOU EXPECT FROM OUR DEDICATED COVERAGE ?</h1>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quidem voluptatibus optio deleniti ab natus, molestiae ex a sint sapiente, at neque cumque quaerat officiis corporis incidunt sit labore ducimus! Natus quis enim amet nisi deleniti, ducimus odio repellendus aliquid, exercitationem ad impedit facilis vero blanditiis laudantium praesentium earum eligendi delectus non consectetur! Tenetur nam quaerat iste hic nostrum maiores et optio, dicta veritatis non beatae ullam praesentium consectetur, laboriosam ducimus quibusdam? Quis voluptatibus mollitia magni.</p>
            <button class="button">Stay Up To Date</button>
        </div>

    </div>
</body>

</html>