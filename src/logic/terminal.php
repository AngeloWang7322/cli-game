<?php

declare(strict_types=1);

$input = trim($_POST["command"] ?? "");
$response = "";
$inputDirectory = implode("/", $_SESSION["curRoom"]->path);

echo "map:<br>" . json_encode($_SESSION["map"]) . "<br>";
echo "cur: <br>" . json_encode($_SESSION["curRoom"]) . "<br>";
try {
    $inputArray = explode(" ", $input);
    $index = 0;

    echo "<br>" . json_encode($inputArray) . "<br>";
    switch ($inputArray[0]) {
        case "cd": {    
            $pathArray = explode("/", $inputArray[1]);
            $_SESSION["curRoom"] =& getRoom($pathArray);
            break;
        }
        case "mkdir": {
            if (sizeof($inputArray) <= 1 && trim($inputArray[1] ?? "") == "") {
                $response = "no directory name provided";
                break;
            }
            array_push($_SESSION["curRoom"]->doors, new Room($inputArray[1]));
            break;
        }
        case "ls": {
            $tempRoomArray = [];
            foreach ($_SESSION["curRoom"]->doors as $door) {
                $tempRoomArray[] = $door->name;
            }
            $response = "- " . implode(", ", $tempRoomArray);
            break;
        }
        case "pwd": {
            $response = implode("/", $_SESSION["curRoom"]->path);
            break;
        }
        case "rm": {
            $removeRoomIndex = hasDoor($_SESSION["curRoom"], $inputArray[1]);
            if ($removeRoomIndex >= 0)
            {
                unset($_SESSION["curRoom"] -> doors[$removeRoomIndex]);
            }
            else{
                throw new Exception("couldn't find '$inputArray[1]'");
            }
        }
    }
} catch (Exception $e) {
    $response = $e->getMessage();
}
$_SESSION["history"][] =
    [
        "directory" => $inputDirectory,
        "command" => $input,
        "response" => $response
    ];

function &getRoom($path, $tempRoom = null): Room
{
    $index = 0;
    if ($tempRoom == null) {
        $tempRoom = $_SESSION["curRoom"];
    }
    switch ($path[0]) {
        case "hall": {
            return getRoomAbsolute($path);
        }
        case '..': {
            if ($_SESSION["curRoom"]->name == "hall") {
                throw new Exception("invalid path");
            }
            while ($path[$index] == '..' && $index < $path) {
                $index++;
            }
            $tempRoom =& getRoomAbsolute(array_slice($_SESSION["curRoom"]->path, 0, count($tempRoom->path) - $index));
        }
        default: {
            if ($index == $path) {
                return $tempRoom;
            }
            return getRoomRelative(array_slice($path, $index), $tempRoom);
        }
    }
}
function &getRoomAbsolute($path): Room
{
    $tempRoom = &$_SESSION["map"];
    $roomIndex = 0;
    for ($i = 1; $i < count($path); $i++) {
        $roomIndex = hasDoor($tempRoom, $path[$i]);
        if ($roomIndex >= 0) {
            $tempRoom =& $tempRoom ->doors[$roomIndex];
        }
        else{
            throw(new Exception("path not found"));
        }
    }
    return $tempRoom;
}
function &getRoomRelative($path, $tempRoom = null): Room
{
    $roomIndex = 0;
    if ($tempRoom == null) {
        $tempRoom =& $_SESSION["curRoom"];
    }
    for ($i = 0; $i < count($path); $i++) {
        $roomIndex = hasDoor($tempRoom, $path[$i]);
        if ($roomIndex >= 0) {
            $tempRoom =& $tempRoom->doors[$roomIndex];
        } else {
            throw (new Exception("path not found"));
        }
    }
    return $tempRoom;
}
function hasDoor($room, $name)
{
    for ($i = 0; $i < count($room->doors); $i++) {
        if ($name == $room->doors[$i]->name) {
            return $i;
        }
    }
    return -1;
}
?>