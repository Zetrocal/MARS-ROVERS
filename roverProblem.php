<?php

// Testing Functions
function areNumbers($testList) {
    
    if (count($testList) == 0) return false;

    foreach ($testList as $element) {
        if (!ctype_digit($element)) return false;
    }

    return true;
}

function isValidCoordinate($testDirection) {
    return $testDirection == "N" || $testDirection == "S" || $testDirection == "W" || $testDirection == "E";
}

function isValidRoverPosition($x, $y, $direction) {
    return areNumbers([$x, $y]) && isValidCoordinate($direction);
}

function isValidRoverInitialPosition($x, $y, $direction, $xFinal, $yFinal) {
    if (isValidRoverPosition($x, $y, $direction)) {
        if (0 <= (int)$x && (int)$x <= $xFinal && 0 <= (int)$y && (int)$y <= $yFinal) {
            return true;
        }
    }
    return false;
}

// Error handlers
function errorPlateuSize($plateauSize) {
    if (count($plateauSize) < 2 || !areNumbers($plateauSize)) {
        echo "Error: Invalid coordinates for Plateu, try again\n";
        return true;
    }
    return false;
}

function errorListIndex($index, $testingList) {
    if ($index + 1 > count($testingList)) {
        echo "Error: No instructions founded for rover in line: " . $index . "\n";
        return true;
    }
    return false;
}

function errorRoverCoordinate($testingList) {
    if (count($testingList) < 3) {
        echo "Error: Insuficient values for roger, needs (x, y, direction) got: " . implode(" ", $testingList) . "\n";
        return true;
    } elseif (!isValidRoverPosition($testingList[0], $testingList[1], $testingList[2])) {
        echo "Error: Invalid values for roger, needs [x(number), y(number), direction(N/S/E/W)]. got: " . implode(" ", $testingList) . "\n";
        return true;
    }
    return false;
}

// Main Functions
function moveRover($x, $y, $orientation, $limitX, $limitY, $instructions) {
    $currentX = $x;
    $currentY = $y;
    $currentOrientation = $orientation;
    $instructions = strtoupper($instructions);

    for ($i = 0; $i < strlen($instructions); $i++) {
        $instruction = $instructions[$i];
        if ($instruction == "M") {
            if ($currentOrientation == "N" && $currentY < $limitY) {
                $currentY++;
            } elseif ($currentOrientation == "S" && $currentY > 0) {
                $currentY--;
            } elseif ($currentOrientation == "E" && $currentX < $limitX) {
                $currentX++;
            } elseif ($currentOrientation == "W" && $currentX > 0) {
                $currentX--;
            }
        } elseif ($instruction == "L") {
            switch ($currentOrientation) {
                case "N":
                    $currentOrientation = "W";
                    break;
                case "W":
                    $currentOrientation = "S";
                    break;
                case "S":
                    $currentOrientation = "E";
                    break;
                case "E":
                    $currentOrientation = "N";
                    break;
            }
        } elseif ($instruction == "R") {
            switch ($currentOrientation) {
                case "N":
                    $currentOrientation = "E";
                    break;
                case "W":
                    $currentOrientation = "N";
                    break;
                case "S":
                    $currentOrientation = "W";
                    break;
                case "E":
                    $currentOrientation = "S";
                    break;
            }
        } else {
            echo "Error: Invalid instruction in string founded: " . $instruction . ", canceling movement\n";
            return [$x, $y, $orientation];
        }
    }
    return [$currentX, $currentY, $currentOrientation];
}

function roversPosition($instructions) {
    $results = [];
    $filteredInputList = array_values(array_filter(explode("\n", $instructions), function($el) {
        return strlen(trim($el)) > 0;
    }));
    
    $plateauSize = array_slice(explode(" ", trim($filteredInputList[0])), 0, 2);

    if (errorPlateuSize($plateauSize)) return $results;

    [$xLimit, $yLimit] = array_map('intval', $plateauSize);

    for ($index = 1; $index < count($filteredInputList); $index += 2) {
        $initialPosition = explode(" ", strtoupper(trim($filteredInputList[$index])));
        if (errorRoverCoordinate($initialPosition)) continue;

        [$xPosition, $yPosition, $direction] = array_slice($initialPosition, 0, 3);

        if (isValidRoverInitialPosition($xPosition, $yPosition, $direction, $xLimit, $yLimit)) {
            if (errorListIndex($index, $filteredInputList)) return $results;

            $instructionList = trim($filteredInputList[$index + 1]);
            $roverFinalPosition = moveRover((int)$xPosition, (int)$yPosition, $direction, $xLimit, $yLimit, $instructionList);
            $stringFinalPosition = implode(" ", $roverFinalPosition);
            $results[] = $stringFinalPosition;
        } else {
            echo "Invalid rover position, continuing with next rover\n";
        }
    }
    return $results;
}

$stringInput = "
5 5
1 2 N 
LMLMLMLMM 
5 3 E
MMRMMRMRRM
";

$roversEndingPositions = roversPosition($stringInput);

foreach ($roversEndingPositions as $roverPosition) {
    echo $roverPosition . "\n";
}
echo "==========\n";
?>