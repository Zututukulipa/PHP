<form class="booking-form" action="../form_handling/exportImport/booking_export.php" method="post">
    <div class="form-header"><h1>Export</h1></div>
    <div class="form-group">
        <select class="form-control" style="width: 100%" name="hotel" id="hotelSelection" onchange="hotelSelected()">
            <?php
            foreach ($hotels as $hot) {
                echo '<option value="' . $hot->idHotel . '">'
                    . $hot->hotelName
                    . '</option>';
            }
            ?>
        </select>
        <span id="hotelLabel" class="form-label">Select Hotel</span>
    </div>
    <input class="submit-btn" type="submit" value="Export"/>
</form>
<form class="booking-form" action="../form_handling/exportImport/booking_import.php" method="post"
      name="frmJSONImport" id="frmJSONImport"
      enctype="multipart/form-data">
    <div class="form-header"><h1>Import</h1></div>
    <div class="form-group">
        <!-- http://jsfiddle.net/4cwpLvae/ -->
        <div class="row" style="border-color: #999999; border-style: solid; border-width: 1px">
            <label for="file" id="fileLbl">
                <div style="text-align: center; width: 50%; padding: 10px; background: #1c2126">
                    <div style="color: #464A52; font-family: Raleway, serif;">
                        <b>SELECT FILE</b>
                    </div>
                </div>
            </label>
            <input class="form-control" onchange="fileSelected()" style="display: none" type="file" name="fileToUpload"
                   id="fileToUpload" accept=".json">
        </div>
        <button type="submit" id="submit" name="import" class="submit-btn">Import</button>
    </div>

</form>
<script type="text/javascript" src="../js/formValidations.js"></script>
<script>
    function hotelSelected() {
        let selected = document.getElementById('hotelSelection');
        let label = document.getElementById('hotelLabel');
        label.innerHTML = selected.options[selected.selectedIndex].text;
    }
</script>
