<!DOCTYPE html>
<html>
<head>
  <title>JQuery LazzyNumeric in Dynamic Table</title>
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
  <script src="js/autoNumeric.js"></script>
  <script src="js/jquery.lazzynumeric.js"></script>
  <script>
    /*** BEGIN: Dynamic table script ***/

    // We still use the old fashioned method here. :-)

    // The number for new row ID.
    var new_row_id = 1;

    // Remove the selected row ID.
    function delRow(id) {
      $("#" + id).remove();
    }

    // Add new row
    function addRow() {
      // Append the new table row in HTML format. Use row ID variable the generate the table row ID.
      $('table#item-list tbody').append('<tr id="newrow-' + new_row_id + '">' +
        '<td><input type="text" name="name[]" /></td>' +
        '<td><input type="text" name="price[]" class="money" /></td>' +
        '<td><input type="button" name="delrow" id="delrow" value="X" onclick="delRow(&quot;newrow-' + new_row_id + '&quot;)" /></td>' +
      '</tr>');

      // Initialize lazzynumeric for the new row.
      $("#newrow-" + new_row_id + " .money").lazzynumeric();

      // Increment the row ID number for the next row.
      new_row_id++;
    }
    /*** END: Dynamic table script ***/
  </script>
  <style>
    /*** BEGIN: Simple CSS for the table ***/
    table {
      width: 400px;
      border-collapse: collapse;
    }

    td, th {
      border:1px solid #999;
      text-align: center;
    }
    /*** END: Simple CSS for the table ***/
  </style>
</head>
<body>
  <h1>JQuery LazzyNumeric in Dynamic Table</h1>
  <p>JQuery LazzyNumeric can be used safely in dynamic table which rows can be added or removed using Javascript.</p>
  <?php
  // Table array for storing the posted value.
  $table = array();

  // Check submitted value to see if the number format is cleared.
  if (isset($_POST["submit"])) {
    // Loop then push the posted value into array.
    // We will use it for initial table content.
    for ($i = 0; $i < count($_POST['name']); $i++) {
      $table[] = array(
        "id" => "postedrow-" . ($i + 1),
        "name" => $_POST['name'][$i],
        "price" => $_POST['price'][$i],
      );
    }

    // Display the posted value.
    echo '<div style="border:1px solid #999;background-color:#ddd">';
    echo 'Yeah... The value is back to the pure number!';
    echo '<pre>';
    print_r($_POST);
    echo '</pre>';
    echo '</div><br/>';
  }
  ?>
  <form action="" method="post">
    <table id="item-list" cellpadding="3" cellspacing="0">
      <thead>
        <tr><th>Item name</th><th>Price</th><th></th></tr>
      </thead>
      <tbody>
        <?php if (count($table) > 0): ?>
          <?php foreach ($table as $row) : ?>
            <tr id="<?php echo $row['id'] ?>"">
              <td><input type="text" name="name[]" value="<?php echo $row['name'] ?>" /></td>
              <td><input type="text" name="price[]" class="money" value="<?php echo $row['price'] ?>" /></td>
              <td><input type="button" name="delrow" id="delrow" value="X" onclick="delRow(&quot;<?php echo $row['id'] ?>&quot;)" /></td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr id="row-1">
            <td><input type="text" name="name[]" /></td>
            <td><input type="text" name="price[]" class="money" /></td>
            <td><input type="button" name="delrow" id="delrow" value="X" onclick="delRow(&quot;row-1&quot;)" /></td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
    <input type="button" name="addrow" id="addrow" value="Add new row" onclick="addRow()" />
    <br/><br/>
    <input type="submit" name="submit" id="submit" value="SUBMIT" />
  </form>
  <script>
    // Initialize lazzynumeric for the existing row.
    $(".money").lazzynumeric();
  </script>
</body>
</html>
