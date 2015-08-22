<?php

    class Monitor {
        private $file;
        private $filepath;
        private $arr;
        private $rain;
        private $totalRain;
        private $temp;
        private $humid;
        private $count;

        public function __construct($file, $path){
            if($file == ''){
                throw new Exception("No file name!", 1);
            }
            if($path == ''){
                throw new Exception("No path name!", 1);
            }

            ##### DATA ######
            $this->rain           = 0;
            $this->rainSum        = 0;
            $this->temp['min']    = 0;
            $this->temp['avg']    = 0;
            $this->temp['max']    = 0;
            $this->temp['total']  = 0;
            $this->humid['min']   = 0;
            $this->humid['avg']   = 0;
            $this->humid['max']   = 0;
            $this->humid['total'] = 0;
            ##### DATA ######

            $this->file = $file;
            $this->filepath = $path;
            $this->reader();
        }

        /**
         *  Create table with $this->arr data
         */
        public function createTable(){
            if(isset($this->arr)){
                for ($i=0; $i < count($this->arr); $i++) {
                    echo "<tr>";
                    for ($j=0; $j < count($this->arr[$i]); $j++) {
                       echo "<td>".$this->arr[$i][$j]."</td>";
                    }
                    echo "</tr>";
                }
            }
        }

        public function maxRain(){
            if(isset($this->rain)){
                echo "<span class='badge'>".$this->totalRain."</span>";
            }
        }

        /**
         *  Caculate average between two variables
         *  @params $a, $b
         */
        private function calcAverage($a, $b) {
            if($a == null || $b == null){
                return 0;
            }
            return $a / $b;
        }

        /**
         *  Return the smallest number between two number
         *  @params $min, $value
         */
        private function verifySmallest($min, $value){
            if($min == 0 || $min > $value){
                return $value;
            }

            return $min;
        }

        /**
         *  Return the biggest number between two number
         *  @params $max, $value
         */
        private function verifyBiggest($max, $value){
            if($max < $value){
                return $value;
            }

            return $max;
        }

        /**
         * Reset all array values to 0.
         * @params $array
         */
        private function reset($array){
            foreach ($array as $key => $value) {
                $array[$key] = 0;
            }
            return $array;
        }

        /**
         * Save data into $this->arr.
         * @params $date, $temp, $humid, $rainSum
         */
        private function saveData($date){
            $auxRain = 0;
            if(!isset($lastPosition)){
                $lastPosition = 0;
            }
            if(!empty($this->arr)){
                if($this->rainSum >= $this->arr[$lastPosition][7]){
                    $lastPosition = (count($this->arr) - 1);
                    if($this->rainSum > $this->rain){
                        $auxRain = $this->rainSum - $this->rain;
                    } else {
                        $auxRain = $this->rain - $this->rainSum;
                    }
                }
            } else {
                $auxRain = $this->rainSum - $this->totalRain;
            }

            $this->arr[] = [$date, round($this->temp['min'], 2), round($this->temp['avg'], 1), round($this->temp['max'], 2), round($this->humid['min'], 2), round($this->humid['avg'], 1), round($this->humid['max'], 2), round($auxRain, 2)];
            $this->totalRain += $auxRain;
        }

        /**
         * Proccess an specific line from file
         * @params $pointer
         */
        private function proccessLineData($line){
            $this->temp['min'] = $this->verifySmallest($this->temp['min'], $line[5]);
            $this->temp['max'] = $this->verifyBiggest($this->temp['max'], $line[5]);
            $this->temp['total'] += $line[5];

            $this->humid['min'] = $this->verifySmallest($this->humid['min'], $line[6]);
            $this->humid['max'] = $this->verifyBiggest($this->humid['max'], $line[6]);
            $this->humid['total'] += $line[6];

            $this->rainSum = $line[7];
            $this->count++;
        }

        /**
         * Proccess received from reading file
         * @params $pointer
         */
        private function proccessData($pointer){
            if(!$pointer){
                throw new Exception("Pointer to reading file not defined!", 1);
            }

            ######### VARIABLES INIT #########
            $auxRain = 0;
            $day = '';
            $month = '';
            $lastDay = '';
            $lastMonth = '';
            $lastDate = '';
            ######### VARIABLES INIT #########

            while (!feof ($pointer)) {
                $line = fgets($pointer, 4096);
                $line = preg_split("/\s+/", $line);

                // Start when line has 9 elements
                if(count($line) == 9){
                    // Spliting date
                    $data = split("/", $line[1]);
                    $day = $data[0];
                    $month = $data[1];

                    // replace ',' by '.'
                    $line[5] = str_replace(",", ".", $line[5]);
                    $line[6] = str_replace(",", ".", $line[6]);
                    $line[7] = str_replace(",", ".", $line[7]);

                    // proccess date info by day
                    if($day == $lastDay || $lastDay == ''){
                        if ($this->count == 0) {
                            $this->totalRain = $line[7];
                        }
                        $this->proccessLineData($line);
                    }
                    else {
                        $this->temp['avg'] = $this->calcAverage($this->temp['total'], $this->count);
                        $this->humid['avg'] = $this->calcAverage($this->humid['total'], $this->count);

                        $this->saveData($lastDate);

                        // reset datas
                        $this->temp = $this->reset($this->temp);
                        $this->humid = $this->reset($this->humid);

                        $this->proccessLineData($line);
                        $this->rain = $line[7];
                    }

                    $lastDay = $day;
                    $lastMonth = $month;
                    $lastDate = $line[1];
                }
            }

            if(feof ($pointer)){
                $this->temp['avg'] = $this->calcAverage($this->temp['total'], $this->count);
                $this->humid['avg'] = $this->calcAverage($this->humid['total'], $this->count);
                $this->saveData($lastDate);
            }
        }

        /**
         *  Open the reading file.
         */
        private function reader(){
            if(!$pointer = fopen ($this->filepath.$this->file, "r")){
                throw new Exception("Can't open the reading file!", 1);
            }
            try{
                $this->proccessData($pointer);
            } catch(Exception $e){
                echo $e;
            }
            fclose($pointer);
        }
    }
