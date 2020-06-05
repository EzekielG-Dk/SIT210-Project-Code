<?php
#initial php code for getting the default or current stored settings
$file = fopen('data.txt', 'r');
$fileRead = fread($file, filesize('data.txt'));
$arrayData = explode(',', $fileRead);
$mode = $arrayData[0];

#Turn the stored time information into the correct time format, so they can be displayed as the current time
$upTime = $arrayData[1];
if(strlen($upTime) % 2 == 1)
{
	$upTimeArray = str_split('0'.$upTime, 2);
}
else
{
	$upTimeArray = str_split($upTime, 2);
}
$upTimeTime = implode(':', $upTimeArray);

#Turn the stored downtime into the right time format
$downTime = $arrayData[2];
if(strlen($downTime) % 2 == 1)
{
        $downTimeArray = str_split('0'.$downTime, 2);
}
else
{
        $downTimeArray = str_split($downTime, 2);
}
$downTimeTime = implode(':', $downTimeArray);
#html code this determines how the site looks and where the post requests are sent
#the initial display is what the current settings are, update settings sends a post
#request to the lower php and raise and lower send them directly to the particle
?>

<!DOCTYPE>
<html>
  <style>
    th, td
    {
      padding: 10px;
    }
  </style>
  <center><h1>Automatic Chicken Coop Door</h1><center>
  <body>
  <form action="" method="POST">
    <label for="mode">Mode</label>
      <select for="mode" name="mode" id="mode">
        <option value="Time">Time</option>
        <option value="Light">Light</otion>
      </select>
    <script>
      var temp = "<?= $mode ?>";
      var modeSelect = document.getElementById('mode');

      for(var i, j = 0; i = modeSelect.options[j]; j++)
      {
        if(i.value == temp)
        {
          modeSelect.selectedIndex = j;
          break;
        }
      }
    </script><br><br>

    <label for="upTime">Open Time</label>
      <input type="time" name="upTime" id="upTime" value="<?= $upTimeTime ?>" step="60">
    <label for="downTime">Close Time</label>
      <input type="time" name="downTime" id="downTime" value="<?= $downTimeTime ?>" step="60"><br><br>
      <input type="submit" name="submit" value="Update Settings">
  </form>

  <table>
  <tr><td>
  <form action="https://api.particle.io/v1/devices/e00fce684ddca9d503a2508e/raise?access_token=64c1dd00054a600d2c2498e62a7145a158bcc2a6" method="POST">
    <input type="submit" name="Raise Door" value="Raise Door">
  </td>
  </form>
  <td>
  <form action="https://api.particle.io/v1/devices/e00fce684ddca9d503a2508e/lower?access_token=64c1dd00054a600d2c2498e62a7145a158bcc2a6" method="POST">
    <input type="submit" name="Lower Door" value="Lower Door">
  </td></tr>
  </form>
  </table>
  </body>
</html>
<?php
#Run when the update settings check is run
if(isset($_POST['upTime']) and isset($_POST['downTime']) and isset($_POST['mode']))
{
	$upTime = str_replace(':', '', $_POST['upTime']);
	$downTime = str_replace(':', '', $_POST['downTime']);
	$mode = $_POST['mode'];
	#doesn't let the user enter the times in the wrong order
        if($upTime >= $downTime)
      	{
                echo  "<p>Close time must be before open time</p>";
        }
        else
        {
		#combine all data values and write them to data.txt file
                $data = $mode.','.$upTime.','.$downTime.',';
	        $fp = fopen('data.txt', 'w');
        	fwrite($fp, $data);
	        fclose($fp);

		#Creates a hidden form that is automatically submitted with the values, this sends the values to the particle device
		?>
		<form action="https://api.particle.io/v1/devices/e00fce684ddca9d503a2508e/updateSettings?access_token=64c1dd00054a600d2c2498e62a7145a158bcc2a6" method="POST" name="myForm">
		<input type="hidden" name="SentSettings" value="<?= $data ?>">
		</form>
		<?php
		echo "<script type=\"text/javascript\">
			document.forms['myForm'].submit();
		      </script>";
        }
}
if(isset($_POST['Raise Door']) or isset($_POST['Lower Door']))
{
	?> <p>Door lowered/raised, will revert in 30seconds</p> <?php
}

?>
