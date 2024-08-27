<?php
 function totalCar($totalWheel,$body,$figures){
    if($body>=1 && $figures>=2 && $totalWheel>=4){
        $totalCar=($totalWheel/4)+($body/($totalWheel/4))+($figures*2);
        return $totalCar;
    }else{
        return 0;
    }
    }

$input=trim(fgets(STDIN));
list($totalWheel,$body,$figures)=explode(" ",$input);
echo totalCar($totalWheel,$body,$figures);
?>
