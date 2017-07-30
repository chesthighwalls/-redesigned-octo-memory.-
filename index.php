<!DOCTYPE html>
<html>
<head>
  <!--
  Copyright 2017 Sean Reeves

  Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

  The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
-->
<meta charset="utf-8" />
<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
<title>Library App 1.0</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<style>
.book {
  line-height: normal;
}
body {
  background-color: #FEFDFB;
}
img {
  height:300px;
  display:block;
  margin-left: auto;
  margin-right: auto;
}
</style>
</head>
<body>
  <nav class="navbar navbar-default">
    <div class="container-fluid">

      <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="<?php echo "http://".$_SERVER['SERVER_NAME']."/"; ?>">Home</a>
      </div>

      <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
        <ul class="nav navbar-nav">
          <?php

          $parts = explode("/", $_SERVER['REQUEST_URI']);

          foreach ($parts as $key => $dir) {

            //test for empty string
            if($dir != ""){

              echo "<li>";

              //create link
              $url = "http://".$_SERVER['SERVER_NAME']."/";
              //create list recursively
              for($i = 1; $i <= $key; $i++){
                $url .= $parts[$i] . "/";
              }
              echo "<a href='$url'>";
              //replace / and uppercase first lettor of directory name
              echo ucfirst(str_replace("/", "",$dir));

              //cleanup
              echo "</a></li>";

            }

          }
          ?>
          <?php

          //create list of directories in the current path
          exec("ls -d */",$folders);

          if (!empty($folders)) {
            ?>
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Next <span class="caret"></span></a>
              <ul class="dropdown-menu">
                <?php
                foreach($folders as $f){

                  if(is_dir($f)){

                    echo "<li><a href='$f'>$f</a></li>";
                    continue;
                  }

                }

                ?>
              </ul>
            </li>
            <?php
          }
          ?>
        </ul>
        <ul class="nav navbar-nav navbar-right">
          <?php
          //poplate list with all pdf files in current directory
          exec("ls *.pdf", $pdf);
          //check if list is empty
          if(!empty($pdf)){
            ?>

            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Book List<span class="caret"></span></a>
              <ul class="dropdown-menu">

                <?php
                //generate list item linked to file for each
                foreach ($pdf as $p) {
                  echo "<li><a href='#" . $p . "'>" . $p . "</a></li>";
                }
                  ?>

                </ul>
              </li>

              <?php
            }
            ?>

          </ul>
        </div>
      </div>
    </nav>
    <div class="container">
      <?php

      //check to see if the pdf list is empty
      if(!empty($pdf)){

        //iterator counts each pdf file
        $count = 0;

        foreach($pdf as $p){
          if ($count == 0) echo "<div class='row'>";

          //if thumbnail is not found  generate one
          if(!file_exists($p.".png")){

            //php must have write permissions
            exec("gs -o ".$p.".png -sDEVICE=pngalpha -dUseCropBox -dFirstPage=1 -dLastPage=1 ".$p, $ouput, $return);
            //non zero values resule in the current row being closed and a new row and column being created.
            if ($return) {
              ?>
            </div>
            <div class="row">
              <div class="col-md-8 col-md-push-2">
                <div class="alert alert-danger" role="alert"><p>Oh, snap! Could not generate thumbnails.</p>
                  <?php echo("<p>gs -o ".$p.".png -sDEVICE=pngalpha -dUseCropBox -dFirstPage=1 -dLastPage=1 ".$p. "Failed!</p>");?>
                </div>

                <div class="alert alert-warning" role="alert">Try running changing your directory permissions.</div>
              </div>
            </div>
            <div class="row">
              <?php
            }
          }

          //create column and populate with thumbnail linked to pdf
          echo "<div class='col-md-4 book'><div class='well'><a href='" . rtrim($p) . "'><img src='" . rtrim($p) . ".png' id='" . rtrim($p) . "'/></a></div></div>";
          $count++;

          // if the iternator is divisable by three the row is closed and the count is reset to zero
          if($count % 3 == 0){
            echo "</div>";
            $count = 0;
          }

        }

        //pdf list is empty
      }else{
        ?>
        <div class="row">
          <div class="col-md-8 col-md-push-2">
            <div class="alert alert-danger" role="alert">Oh, snap! No books found!</div>

            <div class="alert alert-warning" role="alert">What you're looking for might be under the 'Next' menu.</div>
          </div>
        </div>
        <?php
      }
      ?>
    </div>
  </div>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
</body>
</html>
