<table>
    <tr>
        <td><input name="hotelName" type="text" placeholder="Name" value="<?php echo $hotel->hotelName ?>" required>
        </td>
    </tr>
    <tr>
        <td><input name="hotelStreet" type="text" placeholder="Street" value="<?php echo $hotel->street ?>" required/>
        </td>
        <td width="10%"><input name="hotelStreetNr" placeholder="Street Nr." type="number"
                               value="<?php echo $hotel->houseNr ?>" required></td>
    </tr>
    <tr>
        <td><input name="hotelCity" type="text" placeholder="City" value="<?php echo $hotel->city ?>" required/></td>
        <td><input name="hotelZIP" type="number" placeholder="ZIP" value="<?php echo $hotel->zip ?>" required/></td>
    </tr>
</table>
<script type="text/javascript" src="../js/misc.js"></script>
<div class="row" style="border-color: #999999; border-style: solid; border-width: 1px">
    <label for="fileToUpload" id="fileLbl">
        <div style="text-align: center; width: 50%; padding: 10px; background: #1c2126">
            <div style="color: #464A52; font-family: Raleway, serif;">
                <b>SELECT FILE</b>
            </div>
        </div>
    </label>
    <div id="filePath"></div>
    <input class="form-control" onchange="fileSelected()" style="display: none" type="file" name="fileToUpload"
           id="fileToUpload" accept=".jpg, .png, .jpeg">
</div>
<img src="<?php echo $hotel->groundPlanPath ?>">
</table>
<input type="text" value="update" name="action" hidden>
<?php echo '<input type="number" value="' . $_GET['hotelId'] . '" name="hotelId" hidden/>'; ?>
