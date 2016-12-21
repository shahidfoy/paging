<?php
include_once("mysqli_connection.php");

$sql = "SELECT COUNT(id) FROM alphabet";
$query = mysqli_query($connection, $sql);

// total row count
$row = mysqli_fetch_row($query);
$rows = $row[0];

// results displayed per page
$page_rows = 5;

// page number of last page
$last = ceil($rows/$page_rows);
// makes sure $last cannot be less than 1
if($last < 1) {
  $last = 1;
}

// page num
$pagenum = 1;
// get pagenum from URL if it is present otherwise it is 1
if(isset($_GET['pn'])) {
  $pagenum = preg_replace('#[^0-9]#', '', $_GET['pn']);
}

// makes sure the page number isn't below 1, or more then our $last page
if($pagenum < 1) {
  $pagenum = 1;
}
else if($pagenum > $last) {
  $pagenum = $last;
}

// set the rage of rows to query for the chosen $pagenum
$limit = 'LIMIT ' . ($pagenum - 1) * $page_rows . ',' . $page_rows;
$sql = "SELECT id, letter FROM alphabet ORDER BY id DESC $limit";
$query = mysqli_query($connection, $sql);
// shows the user what page they are on, and the total number of pages
$textline1 = "Alphabet (<b>$rows</b>)";
$textline2 = "Page <b>$pagenum</b> of <b>$last</b>";

// establish $paginationCtrls variable
$paginationCtrls = '';
// if more the 1 page
if($last != 1) {
  if($pagenum > 1) {
    $previous = $pagenum - 1;
    $paginationCtrls .= '<a href="'. $_SERVER['PHP_SELF'] .'?pn='.$previous.'">Previous</a> &nbsp; &nbsp; ';

    // Render clickable number links
    for($i = $pagenum - 4; $i < $pagenum; $i++) {
      if($i > 0) {
        $paginationCtrls .= '<a href="'. $_SERVER['PHP_SELF'] .'?pn='.$i.'">'.$i.'</a> &nbsp; ';
      }
    }
  }

  // render the target page number without a link
  $paginationCtrls .= ''. $pagenum . ' &nbsp; ';
  // render clickable number links that appear on the right
  for($i = $pagenum + 1; $i < $last; $i++) {
    $paginationCtrls .= '<a href="'. $_SERVER['PHP_SELF'] .'?pn='.$i.'">'.$i.'</a> &nbsp; ';
    // allows up to 4 pages
    if($i >= $pagenum + 4) {
      break;
    }
  }

  if($pagenum != $last) {
    $next = $pagenum + 1;
    $paginationCtrls .= ' &nbsp; &nbsp; <a href="'. $_SERVER['PHP_SELF'] .'?pn='. $next .'">Next</a> ';
  }
}

$list = '';
while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
  $id = $row["id"];
  $letter = $row["letter"];
  $list .= '<p><a href="letter.php?id='.$id.'">'.$letter.' Letter</a> - Click here to view the Letter<br></p>';
}

// close your database connection
mysqli_close($connection);
 ?>
<!DOCTYPE html>
<html>
<head>
  <title>Paging Example</title>

  <style>
    div#pagination_controls {
      font-size:21px;
    }
    div#pagination_controls > a {
      color: #06F;
    }
    div#pagination_controls > a:visited {
      color: #06F;
    }
  </style>
</head>
<body>
  <div>
    <h2><?php echo $textline1; ?> Paged</h2>
    <p><?php echo $textline2; ?></p>
    <p><?php echo $list; ?></p>
    <div id = "pagination_controls"><?php echo $paginationCtrls; ?></div>
  </div>
</body>
</html>
