<!DOCTYPE html>
<html>

<head>
    <title>Team Status Active</title>
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
    .team_table_container table th{
        background-color: #6c7ae0;
        color: white;
    }
</style>

<body>
    <div class="image_container">
        <img src="" alt="team logo">
    </div>
    <h1>Hello, Team</h1>
    <p>Your team is now active for the upcoming tournament!</p>
    <p>Tournament Name: bla bla</p>
    <p>Start Date: bla bla</p>
    <p>End Date: bla bla</p>
    <p>Location: bla bla</p>
    <h3>Team Name: Pratik Team</h3>
    <h3>Team Coach: Pratik</h3>
    <h3>Phone Number: 9898989898</h3>
    <div class="table_container">
        <div class="team_table_container">
            <table>
                <thead>
                    <tr>
                        <th>Player ID</th>
                        <th>Player Name</th>
                        <th>Phone Mail</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Player 1</td>
                        <td>email 1</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Player 2</td>
                        <td>email 2</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>Player 3</td>
                        <td>email 3</td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td>Player 4</td>
                        <td>email 4</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="signature_container">
        <div>Kind regards,</div>
        <div>Pratik Khadka</div>
    </div>

</body>

</html>