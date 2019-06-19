<?php
    
    // Check if already a path is give, otherweise set path to '.'
    if( isset($_POST['path']) ) {
        $path = $_POST['path'];
    }  else {
        $path = ".";
    }

    // If there is the command to create a folder and folder name is given
    // create a folder with the given name
    if( isset($_POST['create']) ) {
        if( isset($_POST['folder-name']) ) {
            mkdir( $path . "/" . $_POST['folder-name'] );
        }
    }

    // If there is the command to upload a file and a file was uploaded,
    // move the uploaded file from the tmp folder to actual path
    if( isset($_POST['upload']) ) {
        if( isset($_FILES['file']) ) {
            $file_name = $_FILES['file']['name'];
            $file_tmp = $_FILES['file']['tmp_name'];
            move_uploaded_file($file_tmp, $path . "/" . $file_name);
        }
    }

	// Action:
	//  0 = VIEW
	//  1 = DOWNLOAD
	// 	2 = DELETE
    if( isset($_POST['target']) ) {
        $target = $_POST['target'];
        $action = $_POST['action'];
        
        if( is_dir($path . "/" . $target) ) {
            if( $action == 2 ) {
                rmdir($path . "/" . $target);
            } else {
                if( $target != "." ) {
                    if( $target == ".." ) {
                        $split = split("/", $path);
                        $count = count($split);
                        if( $path == "." || $split[$count - 1] == ".." ) {
                            $path = $path . "/" . $target;
                        }  else {
                            $path = ".";
                            for( $i = 1; $i < $count - 1; $i++ ) {
                                $path .= "/" . $split[$i];
                            }
                        }
                    } else {
                        $path = $path . "/" . $target;
                    }
                }
            }
        } else {
            if( $action == 2 ) {
                unlink($path . "/" . $target);
            } else if( $action == 1 ) {
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . basename($target) . '"');
                header("Content-Transfer-Encoding: Binary");
                header('Content-Length:' . filesize($path . "/" . $target));
                
                $file = fopen($path . "/" . $target, "rb");
                while( !feof($file) ) {
                    print fread($file, 1024);
                    flush();
                }
                //readfile($path . "/" . $target);
                fclose($file);
                exit;
            } else {
                die( str_replace("\t", "<span style=\"margin-left: 10px\"></span>", str_replace("\n", "<br>", htmlspecialchars(file_get_contents($path . "/" . $target)))) );
            }
        }
    }

    $dir = scandir($path);
?>

<!DOCTYPE HTML>
<html class="js">
    <head>
        <!-- META -->
        <meta charset="UTF-8">
        
        <!-- CSS -->
        <link href='https://fonts.googleapis.com/css?family=Nunito:400,700' rel='stylesheet' type='text/css'>
        
        <!-- JS -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
        
        <!-- INLINE CSS -->
        <style>
            
            body {
                background-color: #23232e;
            }
            
            button {
                border: none;
                background-color: transparent !important;
            }
            
            h1 {
                color: #ecf0f1;
            }
            
            .text {
                float:left;
                font-size: 20px;
                padding: 15px;
                font-family: Nunito;
            }
            
            .actions {
                display: inline-block; 
                margin-left: 4em; 
                padding: 0.67em;
            }
            
            .actions div {
                margin-left: 20px;
            }
            
            .green-button {
                background-color: #2ecc71 !important;
                font-size: 1.5em;
                cursor: pointer;
                padding: 0.5em;
                border-radius: 6px;
                display: block;
                margin-top: 0.5em;
            }
            
            .text-input {
                font-size: 1.5em;
                border-radius: 6px;
                margin-top: 0.5em;
            }
            
            .icon {
                height: 50px;
                width: 50px;
                display: inline-block;
                float: left;
            }
            
            .icon-small {
                height: 50px;
                width: 50px;
                display: inline-block;
                float: left;
                cursor: pointer;
            }
            
            .icon-small:hover {
                height: 55px;
                width: 55px;
            }
            
            .box {
                padding: 10px;
                display: inline-block;
                background-color: #bdc3c7;
                border-radius: 2px;
                margin: 0.2em;
            }
            
            .box:hover {
                background-color: #ecf0f1;
                cursor: pointer;
            }
            
            .folder-delete {
                background-image: url(data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/PjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+PHN2ZyB2ZXJzaW9uPSIxLjEiIGlkPSJDYXBhXzEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4IiB2aWV3Qm94PSIwIDAgNTkuMzkgNTkuMzkiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDU5LjM5IDU5LjM5OyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+PGc+PGc+PHBvbHlnb24gc3R5bGU9ImZpbGw6IzU1NjA4MDsiIHBvaW50cz0iMjUsMTAuNjk1IDIwLDMuNjk1IDAsMy42OTUgMCwxMC42OTUgMCw1NC42OTUgNTgsNTQuNjk1IDU4LDE3LjY5NSAzMCwxNy42OTUgIi8+PHBvbHlnb24gc3R5bGU9ImZpbGw6IzNENDQ1MTsiIHBvaW50cz0iMzAsMTcuNjk1IDU4LDE3LjY5NSA1OCwxMC42OTUgMjUsMTAuNjk1ICIvPjwvZz48Zz48Y2lyY2xlIHN0eWxlPSJmaWxsOiNFRDcxNjE7IiBjeD0iNDcuMzkiIGN5PSI0My42OTUiIHI9IjEyIi8+PHBhdGggc3R5bGU9ImZpbGw6I0ZGRkZGRjsiIGQ9Ik00OC44MDQsNDMuNjk1bDMuNTM2LTMuNTM2YzAuMzkxLTAuMzkxLDAuMzkxLTEuMDIzLDAtMS40MTRzLTEuMDIzLTAuMzkxLTEuNDE0LDBsLTMuNTM2LDMuNTM2bC0zLjUzNi0zLjUzNmMtMC4zOTEtMC4zOTEtMS4wMjMtMC4zOTEtMS40MTQsMHMtMC4zOTEsMS4wMjMsMCwxLjQxNGwzLjUzNiwzLjUzNkw0Mi40NCw0Ny4yM2MtMC4zOTEsMC4zOTEtMC4zOTEsMS4wMjMsMCwxLjQxNGMwLjE5NSwwLjE5NSwwLjQ1MSwwLjI5MywwLjcwNywwLjI5M3MwLjUxMi0wLjA5OCwwLjcwNy0wLjI5M2wzLjUzNi0zLjUzNmwzLjUzNiwzLjUzNmMwLjE5NSwwLjE5NSwwLjQ1MSwwLjI5MywwLjcwNywwLjI5M3MwLjUxMi0wLjA5OCwwLjcwNy0wLjI5M2MwLjM5MS0wLjM5MSwwLjM5MS0xLjAyMywwLTEuNDE0TDQ4LjgwNCw0My42OTV6Ii8+PC9nPjwvZz48Zz48L2c+PGc+PC9nPjxnPjwvZz48Zz48L2c+PGc+PC9nPjxnPjwvZz48Zz48L2c+PGc+PC9nPjxnPjwvZz48Zz48L2c+PGc+PC9nPjxnPjwvZz48Zz48L2c+PGc+PC9nPjxnPjwvZz48L3N2Zz4=);
            }
            
            .file-download {
                background-image: url(data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/PjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+PHN2ZyB2ZXJzaW9uPSIxLjEiIGlkPSJDYXBhXzEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4IiB2aWV3Qm94PSIwIDAgNTggNTgiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDU4IDU4OyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+PGc+PGc+PHBvbHlnb24gc3R5bGU9ImZpbGw6I0VGRUJERTsiIHBvaW50cz0iNDYuNSwxNCAzMi41LDAgMS41LDAgMS41LDU4IDQ2LjUsNTggIi8+PGc+PHBhdGggc3R5bGU9ImZpbGw6I0Q1RDBCQjsiIGQ9Ik0xMS41LDIzaDI1YzAuNTUyLDAsMS0wLjQ0NywxLTFzLTAuNDQ4LTEtMS0xaC0yNWMtMC41NTIsMC0xLDAuNDQ3LTEsMVMxMC45NDgsMjMsMTEuNSwyM3oiLz48cGF0aCBzdHlsZT0iZmlsbDojRDVEMEJCOyIgZD0iTTExLjUsMTVoMTBjMC41NTIsMCwxLTAuNDQ3LDEtMXMtMC40NDgtMS0xLTFoLTEwYy0wLjU1MiwwLTEsMC40NDctMSwxUzEwLjk0OCwxNSwxMS41LDE1eiIvPjxwYXRoIHN0eWxlPSJmaWxsOiNENUQwQkI7IiBkPSJNMzYuNSwyOWgtMjVjLTAuNTUyLDAtMSwwLjQ0Ny0xLDFzMC40NDgsMSwxLDFoMjVjMC41NTIsMCwxLTAuNDQ3LDEtMVMzNy4wNTIsMjksMzYuNSwyOXoiLz48cGF0aCBzdHlsZT0iZmlsbDojRDVEMEJCOyIgZD0iTTM2LjUsMzdoLTI1Yy0wLjU1MiwwLTEsMC40NDctMSwxczAuNDQ4LDEsMSwxaDI1YzAuNTUyLDAsMS0wLjQ0NywxLTFTMzcuMDUyLDM3LDM2LjUsMzd6Ii8+PHBhdGggc3R5bGU9ImZpbGw6I0Q1RDBCQjsiIGQ9Ik0zNi41LDQ1aC0yNWMtMC41NTIsMC0xLDAuNDQ3LTEsMXMwLjQ0OCwxLDEsMWgyNWMwLjU1MiwwLDEtMC40NDcsMS0xUzM3LjA1Miw0NSwzNi41LDQ1eiIvPjwvZz48cG9seWdvbiBzdHlsZT0iZmlsbDojRDVEMEJCOyIgcG9pbnRzPSIzMi41LDAgMzIuNSwxNCA0Ni41LDE0ICIvPjwvZz48Zz48cmVjdCB4PSIzNC41IiB5PSIzNiIgc3R5bGU9ImZpbGw6IzIxQUU1RTsiIHdpZHRoPSIyMiIgaGVpZ2h0PSIyMiIvPjxyZWN0IHg9IjQ0LjUiIHk9IjM3LjU4NiIgc3R5bGU9ImZpbGw6I0ZGRkZGRjsiIHdpZHRoPSIyIiBoZWlnaHQ9IjE2Ii8+PHBvbHlnb24gc3R5bGU9ImZpbGw6I0ZGRkZGRjsiIHBvaW50cz0iNDUuNSw1NSAzOC41LDQ4LjI5MyAzOS45NzYsNDYuODc5IDQ1LjUsNTIuMTcyIDUxLjAyNCw0Ni44NzkgNTIuNSw0OC4yOTMgIi8+PC9nPjwvZz48Zz48L2c+PGc+PC9nPjxnPjwvZz48Zz48L2c+PGc+PC9nPjxnPjwvZz48Zz48L2c+PGc+PC9nPjxnPjwvZz48Zz48L2c+PGc+PC9nPjxnPjwvZz48Zz48L2c+PGc+PC9nPjxnPjwvZz48L3N2Zz4=);
            }
            
            .file-choose {
                background-image: url(data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/PjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+PHN2ZyB2ZXJzaW9uPSIxLjEiIGlkPSJDYXBhXzEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4IiB2aWV3Qm94PSIwIDAgNTggNTgiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDU4IDU4OyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+PGc+PGc+PHBvbHlnb24gc3R5bGU9ImZpbGw6I0VGRUJERTsiIHBvaW50cz0iNDYuNSwxNCAzMi41LDAgMS41LDAgMS41LDU4IDQ2LjUsNTggIi8+PGc+PHBhdGggc3R5bGU9ImZpbGw6I0Q1RDBCQjsiIGQ9Ik0xMS41LDIzaDI1YzAuNTUyLDAsMS0wLjQ0NywxLTFzLTAuNDQ4LTEtMS0xaC0yNWMtMC41NTIsMC0xLDAuNDQ3LTEsMVMxMC45NDgsMjMsMTEuNSwyM3oiLz48cGF0aCBzdHlsZT0iZmlsbDojRDVEMEJCOyIgZD0iTTExLjUsMTVoMTBjMC41NTIsMCwxLTAuNDQ3LDEtMXMtMC40NDgtMS0xLTFoLTEwYy0wLjU1MiwwLTEsMC40NDctMSwxUzEwLjk0OCwxNSwxMS41LDE1eiIvPjxwYXRoIHN0eWxlPSJmaWxsOiNENUQwQkI7IiBkPSJNMzYuNSwyOWgtMjVjLTAuNTUyLDAtMSwwLjQ0Ny0xLDFzMC40NDgsMSwxLDFoMjVjMC41NTIsMCwxLTAuNDQ3LDEtMVMzNy4wNTIsMjksMzYuNSwyOXoiLz48cGF0aCBzdHlsZT0iZmlsbDojRDVEMEJCOyIgZD0iTTM2LjUsMzdoLTI1Yy0wLjU1MiwwLTEsMC40NDctMSwxczAuNDQ4LDEsMSwxaDI1YzAuNTUyLDAsMS0wLjQ0NywxLTFTMzcuMDUyLDM3LDM2LjUsMzd6Ii8+PHBhdGggc3R5bGU9ImZpbGw6I0Q1RDBCQjsiIGQ9Ik0zNi41LDQ1aC0yNWMtMC41NTIsMC0xLDAuNDQ3LTEsMXMwLjQ0OCwxLDEsMWgyNWMwLjU1MiwwLDEtMC40NDcsMS0xUzM3LjA1Miw0NSwzNi41LDQ1eiIvPjwvZz48cG9seWdvbiBzdHlsZT0iZmlsbDojRDVEMEJCOyIgcG9pbnRzPSIzMi41LDAgMzIuNSwxNCA0Ni41LDE0ICIvPjwvZz48Zz48Y2lyY2xlIHN0eWxlPSJmaWxsOiMyNkI5OTk7IiBjeD0iNDQuNSIgY3k9IjQ2IiByPSIxMiIvPjxwYXRoIHN0eWxlPSJmaWxsOiNGRkZGRkY7IiBkPSJNNTEuMDcxLDQwLjE3OWMtMC40NTUtMC4zMTYtMS4wNzctMC4yMDQtMS4zOTIsMC4yNWwtNS41OTYsOC4wNGwtMy45NDktMy4yNDJjLTAuNDI2LTAuMzUxLTEuMDU3LTAuMjg4LTEuNDA3LDAuMTM5Yy0wLjM1MSwwLjQyNy0wLjI4OSwxLjA1NywwLjEzOSwxLjQwN2w0Ljc4NiwzLjkyOWMwLjE4LDAuMTQ3LDAuNDA0LDAuMjI3LDAuNjM0LDAuMjI3YzAuMDQ1LDAsMC4wOTEtMC4wMDMsMC4xMzctMC4wMDljMC4yNzYtMC4wMzksMC41MjQtMC4xOSwwLjY4NC0wLjQxOWw2LjIxNC04LjkyOUM1MS42MzYsNDEuMTE4LDUxLjUyNCw0MC40OTUsNTEuMDcxLDQwLjE3OXoiLz48L2c+PC9nPjxnPjwvZz48Zz48L2c+PGc+PC9nPjxnPjwvZz48Zz48L2c+PGc+PC9nPjxnPjwvZz48Zz48L2c+PGc+PC9nPjxnPjwvZz48Zz48L2c+PGc+PC9nPjxnPjwvZz48Zz48L2c+PGc+PC9nPjwvc3ZnPg==);
            }
            
            .file-delete {
                background-image: url(data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/PjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+PHN2ZyB2ZXJzaW9uPSIxLjEiIGlkPSJDYXBhXzEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4IiB2aWV3Qm94PSIwIDAgNTggNTgiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDU4IDU4OyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+PGc+PGc+PHBvbHlnb24gc3R5bGU9ImZpbGw6I0VGRUJERTsiIHBvaW50cz0iNDYuNSwxNCAzMi41LDAgMS41LDAgMS41LDU4IDQ2LjUsNTggIi8+PGc+PHBhdGggc3R5bGU9ImZpbGw6I0Q1RDBCQjsiIGQ9Ik0xMS41LDIzaDI1YzAuNTUyLDAsMS0wLjQ0NywxLTFzLTAuNDQ4LTEtMS0xaC0yNWMtMC41NTIsMC0xLDAuNDQ3LTEsMVMxMC45NDgsMjMsMTEuNSwyM3oiLz48cGF0aCBzdHlsZT0iZmlsbDojRDVEMEJCOyIgZD0iTTExLjUsMTVoMTBjMC41NTIsMCwxLTAuNDQ3LDEtMXMtMC40NDgtMS0xLTFoLTEwYy0wLjU1MiwwLTEsMC40NDctMSwxUzEwLjk0OCwxNSwxMS41LDE1eiIvPjxwYXRoIHN0eWxlPSJmaWxsOiNENUQwQkI7IiBkPSJNMzYuNSwyOWgtMjVjLTAuNTUyLDAtMSwwLjQ0Ny0xLDFzMC40NDgsMSwxLDFoMjVjMC41NTIsMCwxLTAuNDQ3LDEtMVMzNy4wNTIsMjksMzYuNSwyOXoiLz48cGF0aCBzdHlsZT0iZmlsbDojRDVEMEJCOyIgZD0iTTM2LjUsMzdoLTI1Yy0wLjU1MiwwLTEsMC40NDctMSwxczAuNDQ4LDEsMSwxaDI1YzAuNTUyLDAsMS0wLjQ0NywxLTFTMzcuMDUyLDM3LDM2LjUsMzd6Ii8+PHBhdGggc3R5bGU9ImZpbGw6I0Q1RDBCQjsiIGQ9Ik0zNi41LDQ1aC0yNWMtMC41NTIsMC0xLDAuNDQ3LTEsMXMwLjQ0OCwxLDEsMWgyNWMwLjU1MiwwLDEtMC40NDcsMS0xUzM3LjA1Miw0NSwzNi41LDQ1eiIvPjwvZz48cG9seWdvbiBzdHlsZT0iZmlsbDojRDVEMEJCOyIgcG9pbnRzPSIzMi41LDAgMzIuNSwxNCA0Ni41LDE0ICIvPjwvZz48Zz48Y2lyY2xlIHN0eWxlPSJmaWxsOiNFRDcxNjE7IiBjeD0iNDQuNSIgY3k9IjQ2IiByPSIxMiIvPjxwYXRoIHN0eWxlPSJmaWxsOiNGRkZGRkY7IiBkPSJNNDUuOTE0LDQ2bDMuNTM2LTMuNTM2YzAuMzkxLTAuMzkxLDAuMzkxLTEuMDIzLDAtMS40MTRzLTEuMDIzLTAuMzkxLTEuNDE0LDBMNDQuNSw0NC41ODZsLTMuNTM2LTMuNTM2Yy0wLjM5MS0wLjM5MS0xLjAyMy0wLjM5MS0xLjQxNCwwcy0wLjM5MSwxLjAyMywwLDEuNDE0TDQzLjA4Niw0NmwtMy41MzYsMy41MzZjLTAuMzkxLDAuMzkxLTAuMzkxLDEuMDIzLDAsMS40MTRjMC4xOTUsMC4xOTUsMC40NTEsMC4yOTMsMC43MDcsMC4yOTNzMC41MTItMC4wOTgsMC43MDctMC4yOTNsMy41MzYtMy41MzZsMy41MzYsMy41MzZjMC4xOTUsMC4xOTUsMC40NTEsMC4yOTMsMC43MDcsMC4yOTNzMC41MTItMC4wOTgsMC43MDctMC4yOTNjMC4zOTEtMC4zOTEsMC4zOTEtMS4wMjMsMC0xLjQxNEw0NS45MTQsNDZ6Ii8+PC9nPjwvZz48Zz48L2c+PGc+PC9nPjxnPjwvZz48Zz48L2c+PGc+PC9nPjxnPjwvZz48Zz48L2c+PGc+PC9nPjxnPjwvZz48Zz48L2c+PGc+PC9nPjxnPjwvZz48Zz48L2c+PGc+PC9nPjxnPjwvZz48L3N2Zz4=);
            }
            
            .folder-choose {
                background-image: url(data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/PjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+PHN2ZyB2ZXJzaW9uPSIxLjEiIGlkPSJDYXBhXzEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4IiB2aWV3Qm94PSIwIDAgNTkuMzkgNTkuMzkiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDU5LjM5IDU5LjM5OyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+PGc+PGc+PHBvbHlnb24gc3R5bGU9ImZpbGw6IzU1NjA4MDsiIHBvaW50cz0iMjUsMTAuNjk1IDIwLDMuNjk1IDAsMy42OTUgMCwxMC42OTUgMCw1NC42OTUgNTgsNTQuNjk1IDU4LDE3LjY5NSAzMCwxNy42OTUgIi8+PHBvbHlnb24gc3R5bGU9ImZpbGw6IzNENDQ1MTsiIHBvaW50cz0iMzAsMTcuNjk1IDU4LDE3LjY5NSA1OCwxMC42OTUgMjUsMTAuNjk1ICIvPjwvZz48Zz48Y2lyY2xlIHN0eWxlPSJmaWxsOiMyNkI5OTk7IiBjeD0iNDcuMzkiIGN5PSI0My42OTUiIHI9IjEyIi8+PHBhdGggc3R5bGU9ImZpbGw6I0ZGRkZGRjsiIGQ9Ik01My45NjEsMzcuODc0Yy0wLjQ1NS0wLjMxNi0xLjA3Ny0wLjIwNC0xLjM5MiwwLjI1bC01LjU5Niw4LjA0bC0zLjk0OS0zLjI0MmMtMC40MjYtMC4zNTEtMS4wNTctMC4yODgtMS40MDcsMC4xMzljLTAuMzUxLDAuNDI3LTAuMjg5LDEuMDU3LDAuMTM5LDEuNDA3bDQuNzg2LDMuOTI5YzAuMTgsMC4xNDcsMC40MDQsMC4yMjcsMC42MzQsMC4yMjdjMC4wNDUsMCwwLjA5MS0wLjAwMywwLjEzNy0wLjAwOWMwLjI3Ni0wLjAzOSwwLjUyNC0wLjE5LDAuNjg0LTAuNDE5bDYuMjE0LTguOTI5QzU0LjUyNiwzOC44MTMsNTQuNDE0LDM4LjE4OSw1My45NjEsMzcuODc0eiIvPjwvZz48L2c+PGc+PC9nPjxnPjwvZz48Zz48L2c+PGc+PC9nPjxnPjwvZz48Zz48L2c+PGc+PC9nPjxnPjwvZz48Zz48L2c+PGc+PC9nPjxnPjwvZz48Zz48L2c+PGc+PC9nPjxnPjwvZz48Zz48L2c+PC9zdmc+);
            }
            
            .folder-create {
                background-image: url(data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/PjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+PHN2ZyB2ZXJzaW9uPSIxLjEiIGlkPSJDYXBhXzEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4IiB2aWV3Qm94PSIwIDAgNTkuMzkgNTkuMzkiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDU5LjM5IDU5LjM5OyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+PGc+PHBvbHlnb24gc3R5bGU9ImZpbGw6IzU1NjA4MDsiIHBvaW50cz0iMjUsMTAuNjk1IDIwLDMuNjk1IDAsMy42OTUgMCwxMC42OTUgMCw1NC42OTUgNTgsNTQuNjk1IDU4LDE3LjY5NSAzMCwxNy42OTUgIi8+PHBvbHlnb24gc3R5bGU9ImZpbGw6IzNENDQ1MTsiIHBvaW50cz0iMzAsMTcuNjk1IDU4LDE3LjY5NSA1OCwxMC42OTUgMjUsMTAuNjk1ICIvPjxnPjxjaXJjbGUgc3R5bGU9ImZpbGw6IzcxQzM4NjsiIGN4PSI0Ny4zOSIgY3k9IjQzLjY5NSIgcj0iMTIiLz48cGF0aCBzdHlsZT0iZmlsbDojRkZGRkZGOyIgZD0iTTUzLjM5LDQyLjY5NWgtNXYtNWMwLTAuNTUyLTAuNDQ4LTEtMS0xcy0xLDAuNDQ4LTEsMXY1aC01Yy0wLjU1MiwwLTEsMC40NDgtMSwxczAuNDQ4LDEsMSwxaDV2NWMwLDAuNTUyLDAuNDQ4LDEsMSwxczEtMC40NDgsMS0xdi01aDVjMC41NTIsMCwxLTAuNDQ4LDEtMVM1My45NDIsNDIuNjk1LDUzLjM5LDQyLjY5NXoiLz48L2c+PC9nPjxnPjwvZz48Zz48L2c+PGc+PC9nPjxnPjwvZz48Zz48L2c+PGc+PC9nPjxnPjwvZz48Zz48L2c+PGc+PC9nPjxnPjwvZz48Zz48L2c+PGc+PC9nPjxnPjwvZz48Zz48L2c+PGc+PC9nPjwvc3ZnPg==);
            }
            
            .file-upload {
                background-image: url(data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/PjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+PHN2ZyB2ZXJzaW9uPSIxLjEiIGlkPSJDYXBhXzEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4IiB2aWV3Qm94PSIwIDAgNTggNTgiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDU4IDU4OyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+PGc+PGc+PHBvbHlnb24gc3R5bGU9ImZpbGw6I0VGRUJERTsiIHBvaW50cz0iNDYsMTQgMzIsMCAxLDAgMSw1OCA0Niw1OCAiLz48Zz48cGF0aCBzdHlsZT0iZmlsbDojRDVEMEJCOyIgZD0iTTExLDIzaDI1YzAuNTUyLDAsMS0wLjQ0NywxLTFzLTAuNDQ4LTEtMS0xSDExYy0wLjU1MiwwLTEsMC40NDctMSwxUzEwLjQ0OCwyMywxMSwyM3oiLz48cGF0aCBzdHlsZT0iZmlsbDojRDVEMEJCOyIgZD0iTTExLDE1aDEwYzAuNTUyLDAsMS0wLjQ0NywxLTFzLTAuNDQ4LTEtMS0xSDExYy0wLjU1MiwwLTEsMC40NDctMSwxUzEwLjQ0OCwxNSwxMSwxNXoiLz48cGF0aCBzdHlsZT0iZmlsbDojRDVEMEJCOyIgZD0iTTM2LDI5SDExYy0wLjU1MiwwLTEsMC40NDctMSwxczAuNDQ4LDEsMSwxaDI1YzAuNTUyLDAsMS0wLjQ0NywxLTFTMzYuNTUyLDI5LDM2LDI5eiIvPjxwYXRoIHN0eWxlPSJmaWxsOiNENUQwQkI7IiBkPSJNMzYsMzdIMTFjLTAuNTUyLDAtMSwwLjQ0Ny0xLDFzMC40NDgsMSwxLDFoMjVjMC41NTIsMCwxLTAuNDQ3LDEtMVMzNi41NTIsMzcsMzYsMzd6Ii8+PHBhdGggc3R5bGU9ImZpbGw6I0Q1RDBCQjsiIGQ9Ik0zNiw0NUgxMWMtMC41NTIsMC0xLDAuNDQ3LTEsMXMwLjQ0OCwxLDEsMWgyNWMwLjU1MiwwLDEtMC40NDcsMS0xUzM2LjU1Miw0NSwzNiw0NXoiLz48L2c+PHBvbHlnb24gc3R5bGU9ImZpbGw6I0Q1RDBCQjsiIHBvaW50cz0iMzIsMCAzMiwxNCA0NiwxNCAiLz48L2c+PGc+PHJlY3QgeD0iMzUiIHk9IjM2IiBzdHlsZT0iZmlsbDojNDhBMERDOyIgd2lkdGg9IjIyIiBoZWlnaHQ9IjIyIi8+PHJlY3QgeD0iNDUiIHk9IjQyIiBzdHlsZT0iZmlsbDojRkZGRkZGOyIgd2lkdGg9IjIiIGhlaWdodD0iMTYiLz48cG9seWdvbiBzdHlsZT0iZmlsbDojRkZGRkZGOyIgcG9pbnRzPSI1MS4yOTMsNDguNzA3IDQ2LDQzLjQxNCA0MC43MDcsNDguNzA3IDM5LjI5Myw0Ny4yOTMgNDYsNDAuNTg2IDUyLjcwNyw0Ny4yOTMgIi8+PC9nPjwvZz48Zz48L2c+PGc+PC9nPjxnPjwvZz48Zz48L2c+PGc+PC9nPjxnPjwvZz48Zz48L2c+PGc+PC9nPjxnPjwvZz48Zz48L2c+PGc+PC9nPjxnPjwvZz48Zz48L2c+PGc+PC9nPjxnPjwvZz48L3N2Zz4=);
            }
            
            .file-view {
                background-image: url(data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/PjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+PHN2ZyB2ZXJzaW9uPSIxLjEiIGlkPSJDYXBhXzEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4IiB2aWV3Qm94PSIwIDAgNTguMTk1IDU4LjE5NSIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgNTguMTk1IDU4LjE5NTsiIHhtbDpzcGFjZT0icHJlc2VydmUiPjxnPjxnPjxwb2x5Z29uIHN0eWxlPSJmaWxsOiNFRkVCREU7IiBwb2ludHM9IjQ1LDE0LjA5NyAzMSwwLjA5NyAwLDAuMDk3IDAsNTguMDk3IDQ1LDU4LjA5NyAiLz48Zz48cGF0aCBzdHlsZT0iZmlsbDojRDVEMEJCOyIgZD0iTTEwLDIzLjA5N2gyNWMwLjU1MiwwLDEtMC40NDcsMS0xcy0wLjQ0OC0xLTEtMUgxMGMtMC41NTIsMC0xLDAuNDQ3LTEsMVM5LjQ0OCwyMy4wOTcsMTAsMjMuMDk3eiIvPjxwYXRoIHN0eWxlPSJmaWxsOiNENUQwQkI7IiBkPSJNMTAsMTUuMDk3aDEwYzAuNTUyLDAsMS0wLjQ0NywxLTFzLTAuNDQ4LTEtMS0xSDEwYy0wLjU1MiwwLTEsMC40NDctMSwxUzkuNDQ4LDE1LjA5NywxMCwxNS4wOTd6Ii8+PHBhdGggc3R5bGU9ImZpbGw6I0Q1RDBCQjsiIGQ9Ik0zNSwyOS4wOTdIMTBjLTAuNTUyLDAtMSwwLjQ0Ny0xLDFzMC40NDgsMSwxLDFoMjVjMC41NTIsMCwxLTAuNDQ3LDEtMVMzNS41NTIsMjkuMDk3LDM1LDI5LjA5N3oiLz48cGF0aCBzdHlsZT0iZmlsbDojRDVEMEJCOyIgZD0iTTM1LDM3LjA5N0gxMGMtMC41NTIsMC0xLDAuNDQ3LTEsMXMwLjQ0OCwxLDEsMWgyNWMwLjU1MiwwLDEtMC40NDcsMS0xUzM1LjU1MiwzNy4wOTcsMzUsMzcuMDk3eiIvPjxwYXRoIHN0eWxlPSJmaWxsOiNENUQwQkI7IiBkPSJNMzUsNDUuMDk3SDEwYy0wLjU1MiwwLTEsMC40NDctMSwxczAuNDQ4LDEsMSwxaDI1YzAuNTUyLDAsMS0wLjQ0NywxLTFTMzUuNTUyLDQ1LjA5NywzNSw0NS4wOTd6Ii8+PC9nPjxwb2x5Z29uIHN0eWxlPSJmaWxsOiNENUQwQkI7IiBwb2ludHM9IjMxLDAuMDk3IDMxLDE0LjA5NyA0NSwxNC4wOTcgIi8+PC9nPjxnPjxwYXRoIHN0eWxlPSJmaWxsOiNGRkZGRkY7IiBkPSJNNTcsNDguMjg5bC0wLjEwNywwLjE2M2MtNy4xMjEsMTAuODc2LTE4Ljc3MywxMC44NzYtMjUuODkzLDBsMCwwbDAuMTA3LTAuMTYzQzM4LjIyNywzNy40MTIsNDkuODc5LDM3LjQxMiw1Nyw0OC4yODlMNTcsNDguMjg5eiIvPjxjaXJjbGUgc3R5bGU9ImZpbGw6IzU1NjA4MDsiIGN4PSI0My43NjQiIGN5PSI0Ni4wMDciIHI9IjUuOTA5Ii8+PHBhdGggc3R5bGU9ImZpbGw6Izg2OTdDQjsiIGQ9Ik00My45NDcsNTcuNjA5Yy01LjI1NCwwLTEwLjE0OC0zLjA1OC0xMy43ODMtOC42MDlsLTAuMzU4LTAuNTQ3bDAuNDY1LTAuNzExYzMuNjM1LTUuNTUyLDguNTMtOC42MDksMTMuNzg0LTguNjA5YzUuMjUzLDAsMTAuMTQ4LDMuMDU3LDEzLjc4Myw4LjYwOWwwLjM1OCwwLjU0N2wtMC40NjUsMC43MTFDNTQuMDk1LDU0LjU1Miw0OS4yLDU3LjYwOSw0My45NDcsNTcuNjA5eiBNMzIuMjAzLDQ4LjQ0OGMzLjIwNiw0LjYyNCw3LjM1Niw3LjE2MSwxMS43NDQsNy4xNjFjNC40MzYsMCw4LjYzLTIuNTk0LDExLjg1LTcuMzE3Yy0zLjIwNi00LjYyNC03LjM1Ni03LjE2MS0xMS43NDMtNy4xNjFDMzkuNjE3LDQxLjEzMiwzNS40MjMsNDMuNzI1LDMyLjIwMyw0OC40NDh6Ii8+PC9nPjwvZz48Zz48L2c+PGc+PC9nPjxnPjwvZz48Zz48L2c+PGc+PC9nPjxnPjwvZz48Zz48L2c+PGc+PC9nPjxnPjwvZz48Zz48L2c+PGc+PC9nPjxnPjwvZz48Zz48L2c+PGc+PC9nPjxnPjwvZz48L3N2Zz4=);
            }

            .folder-view {
                background-image: url(data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/PjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+PHN2ZyB2ZXJzaW9uPSIxLjEiIGlkPSJDYXBhXzEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4IiB2aWV3Qm94PSIwIDAgNTkgNTkiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDU5IDU5OyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+PGc+PGc+PHBvbHlnb24gc3R5bGU9ImZpbGw6IzU1NjA4MDsiIHBvaW50cz0iMjUsMTAgMjAsMyAwLDMgMCwxMCAwLDU0IDU4LDU0IDU4LDE3IDMwLDE3ICIvPjxwb2x5Z29uIHN0eWxlPSJmaWxsOiMzRDQ0NTE7IiBwb2ludHM9IjMwLDE3IDU4LDE3IDU4LDEwIDI1LDEwICIvPjwvZz48Zz48cGF0aCBzdHlsZT0iZmlsbDojRkZGRkZGOyIgZD0iTTU3LjgwNSw0Ni42NzlsLTAuMTA3LDAuMTYzYy03LjEyMSwxMC44NzYtMTguNzczLDEwLjg3Ni0yNS44OTMsMGwwLDBsMC4xMDctMC4xNjNDMzkuMDMzLDM1LjgwMyw1MC42ODUsMzUuODAzLDU3LjgwNSw0Ni42NzlMNTcuODA1LDQ2LjY3OXoiLz48Y2lyY2xlIHN0eWxlPSJmaWxsOiM1NTYwODA7IiBjeD0iNDQuNTY5IiBjeT0iNDQuMzk3IiByPSI1LjkwOSIvPjxwYXRoIHN0eWxlPSJmaWxsOiNCMUQzRUY7IiBkPSJNNDQuNzUyLDU2Yy01LjI1NCwwLTEwLjE0OC0zLjA1OC0xMy43ODMtOC42MDlsLTAuMzU4LTAuNTQ3bDAuNDY1LTAuNzExYzMuNjM1LTUuNTUyLDguNTMtOC42MDksMTMuNzg0LTguNjA5YzUuMjUzLDAsMTAuMTQ4LDMuMDU3LDEzLjc4Myw4LjYwOUw1OSw0Ni42NzlsLTAuNDY1LDAuNzExQzU0LjksNTIuOTQyLDUwLjAwNSw1Niw0NC43NTIsNTZ6IE0zMy4wMDgsNDYuODM5QzM2LjIxNCw1MS40NjMsNDAuMzY1LDU0LDQ0Ljc1Miw1NGM0LjQzNiwwLDguNjMtMi41OTQsMTEuODUtNy4zMTdjLTMuMjA2LTQuNjI0LTcuMzU2LTcuMTYxLTExLjc0My03LjE2MUM0MC40MjIsMzkuNTIyLDM2LjIyOSw0Mi4xMTYsMzMuMDA4LDQ2LjgzOXoiLz48L2c+PC9nPjxnPjwvZz48Zz48L2c+PGc+PC9nPjxnPjwvZz48Zz48L2c+PGc+PC9nPjxnPjwvZz48Zz48L2c+PGc+PC9nPjxnPjwvZz48Zz48L2c+PGc+PC9nPjxnPjwvZz48Zz48L2c+PGc+PC9nPjwvc3ZnPg==);
            }
            
            .js .inputfile {
                width: 0.1px;
                height: 0.1px;
                opacity: 0;
                overflow: hidden;
                position: absolute;
                z-index: -1;
            }
            
            .inputfile + label {
                max-width: 80%;
                font-size: 1.25rem;
                /* 20px */
                font-weight: 700;
                text-overflow: ellipsis;
                white-space: nowrap;
                cursor: pointer;
                display: inline-block;
                overflow: hidden;
                padding: 0.625rem 1.25rem;
                /* 10px 20px */
            }
            
            .inputfile:focus + label, .inputfile.has-focus + label {
                outline: 1px dotted #000;
                outline: -webkit-focus-ring-color auto 5px;
            }
            
            .inputfile-6 + label {
                color: #444444;
            }
            
            .inputfile-6 + label {
                border: 1px solid #444444;
                background-color: #FBFBFB;
                padding: 0;
            }
            
            .inputfile-6:focus + label, .inputfile-6.has-focus + label, .inputfile-6 + label:hover {
                border-color: #444444;
            }
            
            .inputfile-6 + label span, .inputfile-6 + label strong {
                padding: 0.625rem 1.25rem;
                /* 10px 20px */
            }
            
            .inputfile-6 + label span {
                width: 200px;
                min-height: 2em;
                display: inline-block;
                text-overflow: ellipsis;
                white-space: nowrap;
                overflow: hidden;
                vertical-align: top;
            }
            
            .inputfile-6 + label strong {
                height: 100%;
                color: #FBFBFB;
                background-color: #444444;
                display: inline-block;
            }
            
            .inputfile-6:focus + label strong, .inputfile-6.has-focus + label strong, .inputfile-6 + label:hover strong {
                background-color: #111;
            }
            
            @media screen and (max-width: 50em) {
                .inputfile-6 + label strong {
                    display: block;
                }
            }
            
            *, *:after, *:before {
                -webkit-box-sizing: border-box;
                box-sizing: border-box;
            }
        </style>
        
        <!-- INLINE JS -->
        <script>
            folderStatus = true;
            fileStatus = 0; // 0 = VIEW     1 = DOWNLOAD    2 = DELETE
            createFolder = false;
            uploadStatus = false;
            
            function toggleFolder() {
                folderStatus = !folderStatus;
                
                $('#folderStatus').toggleClass("folder-delete");
                $('#folderStatus').toggleClass("folder-view");
                $('.folder').toggleClass("folder-delete");
                $('.folder').toggleClass("folder-view");
                $('input[name=\'action\']').attr("value", !folderStatus ? 2 : 0);
            }
            
            function fileDownload() {
                if( fileStatus == 1 ) {
                    $('#fileDownload').removeClass('file-view');
                    $('#fileDownload').addClass('file-download');
                    
                    $('.file').removeClass('file-download file-delete');
                    $('.file').addClass('file-view');
                    
                    fileStatus = 0;
                    $('input[name=\'action\']').attr("value", 0);
                } else {
                    $('#fileDownload').removeClass('file-download');
                    $('#fileDownload').addClass('file-view');
                    
                    $('.file').removeClass('file-view file-delete');
                    $('.file').addClass('file-download');
                    
                    fileStatus = 1;
                    $('input[name=\'action\']').attr("value", 1);
                }
                
                $('#fileDelete').removeClass('file-view');
                $('#fileDelete').addClass('file-delete');
            }
            
            function fileDelete() {
                if( fileStatus == 2 ) {
                    $('#fileDelete').removeClass('file-view');
                    $('#fileDelete').addClass('file-delete');
                    
                    $('.file').removeClass('file-download file-delete');
                    $('.file').addClass('file-view');
                    
                    fileStatus = 0;
                    $('input[name=\'action\']').attr("value", 0);
                } else {
                    $('#fileDelete').removeClass('file-delete');
                    $('#fileDelete').addClass('file-view');
                    
                    $('.file').removeClass('file-view file-download');
                    $('.file').addClass('file-delete');
                   
                    fileStatus = 2;
                    $('input[name=\'action\']').attr("value", 2);
                }
                
                $('#fileDownload').removeClass('file-view');
                $('#fileDownload').addClass('file-download');
            }
            
            function toggleCreate() {
                createFolder = !createFolder;
                
                $('#file-upload').slideUp();
                uploadStatus = false;

                if( createFolder ) {
                    $('#folder-name').slideDown();
                } else {
                    $('#folder-name').slideUp();
                }
            }
            
            function toggleUpload() {
                uploadStatus = !uploadStatus;
                
                $('#folder-name').slideUp();
                createFolder = false;

                if( uploadStatus ) {
                    $('#file-upload').slideDown();
                } else {
                    $('#file-upload').slideUp();
                }
            }
        </script>
    </head>
    
    <body>
        <h1 style="float: left">File-Browser</h1>
        <div class="actions">
            <div onclick="toggleCreate();" class="icon-small folder-create"></div>
            <div onclick="toggleFolder();" class="icon-small folder-delete" id="folderStatus"></div>
            <div onclick="toggleUpload();" class="icon-small file-upload"></div>
            <div onclick="fileDownload();" class="icon-small file-download" id="fileDownload"></div>
            <div onclick="fileDelete();" class="icon-small file-delete" id="fileDelete"></div>
        </div>
        
        <br>
        <br>
        <br>
        
        <content>
            <h1><?php echo $path; ?></h1>
            <form action="" method="post" enctype="multipart/form-data">
                <input type="hidden" name="path" value="<?php echo $path; ?>">;
                <input type="hidden" name="delete" value="false">
                <input type="hidden" name="action" value="0">
                
                <div id="folder-name" style="display: none; padding:10px">
                    <input class="text-input" type="text" name="folder-name" value="Neuer Ordner">
                    <button type="submit" name="create" class="green-button">Ordner erstellen</button>
                    <br>
                </div>
                
                <div id="file-upload" style="padding: 10px; display: none">
                    <input type="file" name="file" id="file-7" class="inputfile inputfile-6" data-multiple-caption="{count} files selected" multiple />
                    <label for="file-7"><span></span> <strong> Datei w&auml;hlen&hellip;</strong></label>
                    <button type="submit" name="upload" class="green-button">Datei hochladen</button>
                </div>
                
                <?php
                    foreach($dir AS $content) {
                        if( is_dir($path . "/" . $content) ) {
                            echo '<button type="submit" name="target" value="' . $content . '"><div class="box"><div class="icon folder folder-view"></div><div class="text">' . $content . '</div></div></button>';
                        } else {
                            echo '<button type="submit" name="target" value="' . $content . '"><div class="box"><div class="icon file file-view"></div><div class="text">' . $content . '</div></div></button>';
                        }
                    }
                ?>
            </form>
        </content>
    </body>
    
    <script>
        ;( function(document, window, index) {
            var inputs = document.querySelectorAll( '.inputfile' );
            Array.prototype.forEach.call( inputs, function( input ) {
                var label = input.nextElementSibling, labelVal = label.innerHTML;
                
                input.addEventListener('change', function( e ) {
                    var fileName = '';
                    if( this.files && this.files.length > 1 ) 
                        fileName = ( this.getAttribute( 'data-multiple-caption' ) || '' ).replace( '{count}', this.files.length );
                    else
                        fileName = e.target.value.split( '\\' ).pop();
                    
                    if( fileName )
                        label.querySelector( 'span' ).innerHTML = fileName;
                    else 
                        label.innerHTML = labelVal;
                });
                
                // Firefox Bugfix
                input.addEventListener( 'focus', function(){ input.classList.add( 'has-focus' ); });
                input.addEventListener( 'blur', function(){ input.classList.remove( 'has-focus' ); });
            });
        }( document, window, 0));
    </script>
</html>
