<?php
declare(strict_types=1);

$response = "";
$inputDirectory = implode("/", $_SESSION["curRoom"]->path);
$inputCommand = $_POST["command"];

$inputArgs = organizeInput(explode(" ", $inputCommand));

echo "map:<br>" . json_encode($_SESSION["map"]) . "<br>";
// echo "curRoom: <br>" . json_encode($_SESSION["curRoom"]) . "<br>";
try {
    echo "<br>" . json_encode($inputArray) . "<br>";
    switch ($inputArgs["command"]) {
        case "cd": {
                if (count($inputArgs["path"]) == 0) {
                    throw new Exception("no path provided");
                }
                $_SESSION["user"]->curMana -= (count($inputArgs["path"]) - 1) * 2;
                $_SESSION["curRoom"] = &getRoom($inputArgs["path"]);
                break;
            }
        case "mkdir": {
                if ($inputArgs["path"] == null) {
                    throw new Exception ("no directory name provided");
                    break;
                }
                $roomName = $inputArgs["path"][count($inputArgs["path"])- 1];
                $tempRoom =& getRoom(array_slice($inputArgs["path"], 0, count($inputArgs["path"]) - 1));
                $tempRoom -> doors[$roomName] = new Room($roomName);
                break;
            }
        case "ls": {
                $lsArray = [];  
                foreach ($_SESSION["curRoom"]->doors as $door) {
                    $lsArray[] = $door->name;
                }
                foreach ($_SESSION["curRoom"]->items as $element) {
                    $lsArray[] = $element->name;
                }
                $response = "- " . implode(", ", $lsArray);
                break;
            }
        case "pwd": {
                $response = implode("/", $_SESSION["curRoom"]->path);
                break;
            }
        case "rm": {
                // echo "<br><br>aa: " . json_encode(explode("/", $inputArray[1]));
                deleteElement($inputArgs["path"]);
                break;
            }
        case "cat": {
                $item = &getItem(null, $inputArray[1]);
                // echo "found item:" . json_encode($item);
                $_SESSION["openedScroll"]->header = $item->name;
                $_SESSION["openedScroll"]->content = $item->content;
                $_SESSION["openedScroll"]->isOpen = true;
                break;
            }
        default: {
                $item = &getItem(null, $inputArray[0]);
                $item->executeAction();
                $fileType = stristr($inputArray[0], '.');
            }
    }
} catch (Exception $e) {
    editMana(amount: 10);
    $response = $e->getMessage();
}
$_SESSION["history"][] =
    [
        "directory" => $inputDirectory,
        "command" => $inputCommand,
        "response" => $response
    ];
function organizeInput(array $inputArray)
{
    $inputArgs = [
        "command" => $inputArray[0],
        "path" => [],
        "flags" => [],
    ];
    for ($i = 1; $i < count($inputArray); $i++) {
        if ($inputArray[$i][0] == '-') {
            $inputArgs["flags"][] = $inputArray[$i];
        } else {
            $inputArgs["path"] = explode("/", $inputArray[$i]);
        }
    }
    return $inputArgs;
}
function &getRoom($path, $tempRoom = null): Room
{
    $index = 0;
    if ($tempRoom == null) {
        $tempRoom =& $_SESSION["curRoom"];
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
                $tempRoom = &getRoomAbsolute(array_slice($_SESSION["curRoom"]->path, 0, count($tempRoom->path) - $index));
            }
        default: {
                if ($index == $path) {
                    return $tempRoom;
                }
                return getRoomRelative(array_slice($path, 0, count($tempRoom -> path) - $index), $tempRoom);
            }
    }
}
function &getRoomAbsolute($path): Room
{
    $tempRoom = &$_SESSION["map"];
    for ($i = 1; $i < count($path); $i++) {
        if (in_array($path[$i], array_keys($tempRoom -> doors))) {
            $tempRoom = &$tempRoom->doors[$path[$i]];
        } else {
            throw (new Exception("path not found"));
        }
    }
    return $tempRoom;
}
function &getRoomRelative($path, $tempRoom = null): Room
{
    if ($tempRoom == null) {
        $tempRoom = &$_SESSION["curRoom"];
    }
    for ($i = 0; $i < count($path); $i++) {
        if (in_array($path[$i], array_keys($tempRoom -> doors))) {
            $tempRoom = &$tempRoom->doors[$path[$i]];
        } else {
            throw (new Exception("path not found"));
        }
    }
    return $tempRoom;
}
function &getItem($path = null, $itemName): Item
{
    echo "getting item $itemName<br>";
    $tempRoom = &$_SESSION["curRoom"];
    if ($path == null) {
        $path = &$_SESSION["curRoom"]->path;
    } else {
        $tempRoom = &getRoom(array_splice($path, 0, count($path) - 2));
    }

    if (in_array($path[count($path) - 1], array_keys($tempRoom->items))) {
        return $tempRoom->items[$path[count($path) - 1]];
    } else {
        throw new Exception("item not found");
    }
}
function hasElementWithName($array, $name)
{
    echo "<br> name: $name<br>";
    for ($i = 0; $i < count($array); $i++) {
        echo "comparing $name with " . $array[$i]->name . "<br>";
        if ($name == $array[$i]->name) {
            return $i;
        }
    }
    return -1;
}
function deleteElement($path)
{
    if (count($path) > 2) {
        $tempRoom = &getRoom(array_slice($path, 0, count($path) - 2));
    } else {
        $tempRoom = &$_SESSION["curRoom"];
    }

    if(in_array($path[count($path) - 1], array_keys($tempRoom ->doors))){
        unset($tempRoom ->doors[$path[count($path) - 1]]); 
    }
    else if(in_array($path[count($path) - 1], array_keys( $tempRoom ->items))){
        unset($tempRoom ->items[$path[count($path) - 1]]); 
    }
    else{
        throw new Exception("element not found");
    }

}
function editMana($amount)
{
    $_SESSION["user"]->curMana -= $amount;
}
