#string input

#---- Testing Functions -----
def areNumbers(testList):
    if len(testList) == 0: return False

    for element in testList:
        if(not element.isdigit()): return False

    return True


def isValidCoordinate(testDirection):
    if testDirection == "N" or testDirection == "S" or testDirection == "W" or testDirection == "E":
        return True
    return False


def isValidRoverPosition(x, y, direction):
    if(areNumbers((x,y)) and isValidCoordinate(direction)): 
        return True
    return False

def isValidRoverInitialPosition(x, y, direction, xFinal, yFinal):
    if isValidRoverPosition(x, y, direction):
        if 0 <= int(x) <= xFinal and 0 <= int(y) <= yFinal:
            return True
    return False

#------ Error handlers -----
def errorPlateuSize(plateauSize):
    if len(plateauSize) < 2 or not areNumbers(plateauSize):
        print("Error: Invalid coordinates for Plateu, try again")
        return True
    return False

def errorListIndex(index, testingList):
    if index+1 > len(testingList):
        print("Error: No instructions founded for rover in line: " + index)
        return True
    return False

def errorRoverCoordinate(testingList):
    if len(testingList) < 3:
        print("Error: Insuficient values for roger, needs (x, y, direction) got: " + " ".join([str(x) for x in testingList]))
        return True
    elif not isValidRoverPosition(testingList[0], testingList[1], testingList[2]):
        print("Error: Invalid values for roger, needs [x(number), y(number), direction(N/S/E/W)]. got: " + " ".join([str(x) for x in testingList]))
        return True
    return False



#----- Main Functions ----


def moveRover(x, y, orientation, limitX, limitY, instructions):
    currentX = x
    currentY = y
    currentOrientation = orientation
    for instruction in instructions.upper():
        if(instruction == "M"):
            if(currentOrientation == "N" and currentY < limitY):
                currentY+=1
            elif(currentOrientation == "S" and currentY > 0):
                currentY-=1
            elif(currentOrientation == "E" and currentX < limitX):
                currentX+=1
            elif(currentOrientation == "W" and currentX > 0):
                currentX-=1
        elif(instruction == "L"):
            match currentOrientation:
                case "N":
                    currentOrientation = "W"
                case "W":
                    currentOrientation = "S"
                case "S":
                    currentOrientation = "E"
                case "E":
                    currentOrientation = "N"
        elif(instruction == "R"):
            match currentOrientation:
                case "N":
                    currentOrientation = "E"
                case "W":
                    currentOrientation = "N"
                case "S":
                    currentOrientation = "W"
                case "E":
                    currentOrientation = "S"
        else:
            print("Error: Invalid instruction in string founded: " + instruction+ ", canceling movement")
            return [x, y, orientation]
    return [currentX, currentY, currentOrientation]



def roversPosition(instructions):

    results = []
    filteredInputList = list(filter(lambda x: len(x) > 0, instructions.split("\n")))
    plateauSize = filteredInputList[0].strip().split(" ")[0:2]

    if errorPlateuSize(plateauSize): return results
    [xLimit, yLimit] = map(lambda coordinate: int(coordinate), plateauSize)
    
    for index in range(1, len(filteredInputList), 2):

        initialPosition = filteredInputList[index].strip().upper().split()

        if errorRoverCoordinate(initialPosition): continue
        [xPosition, yPosition, direction] = initialPosition[0:3]

        if(isValidRoverInitialPosition(xPosition, yPosition, direction, xLimit, yLimit)):
            if errorListIndex(index, filteredInputList): return results
            instructionList = filteredInputList[index+1]
            
            roverFinalPosition = moveRover(int(xPosition), int(yPosition), direction, xLimit, yLimit, instructionList.strip())
            stringFinalPosition = " ".join([str(x) for x in roverFinalPosition])
            results.append(stringFinalPosition)

        else:
            print("Invalid rover position, continuing with next rover")
    return results
        
        
stringInput = """
5 5
1 2 N 
LMLMLMLMM 
5 3 E
MMRMMRMRRM
"""

roversEndingPositions = roversPosition(stringInput)

for roverPosition in roversEndingPositions:
    print(roverPosition)
print("==========")
