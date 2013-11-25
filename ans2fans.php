<?php

 $file = "8bit.ans";

 $content = file_get_contents($file);

 $string = str_split($content);
 $len = strlen($content);

 $fore=7;
 $back=0;
 $highlight=false;
 $underline=false;
 $blinking=false;
 $inverted=false;
 $hidden=false;

 $out=array();
 $i=0;
 for ( $i=0; $i<$len; $i++ ) {
  $o=ord($string[$i]);
  switch ( $o ) {
   case 0: case 7: case 13: break;
   case 10: $out[]="%R"; break;
   case 27:
     {
      $type='m';
      $code="";
      $j=$i+1;
      $stop=false;
      while ( $j+1 < $len && $stop===false ) {
       $piece=$string[$j++];
       if ( $piece == '[' ) {} else
       if ( $piece == '?' ) { $type = '?'; } else
       if ( $piece == 'h' ) { $type = 'h'; $stop=true; } else
       if ( $piece == 'm' ) { $type = 'm'; $stop=true; } else
       if ( $piece == 'C' ) { $type = 'C'; $stop=true; } else
       $code.=$piece;
      }
      $i=$j-1;
      echo 'Code: '.$code.' Type: '.$type." ";
      $parts = explode(';',$code);
      foreach ( $parts as &$part ) $part=intval($part);
      switch ( $type ) {
       case 'm':
         {
          $pieces=count($parts);
          for ( $k=0; $k<$pieces; $k++ ) {
           $value=intval($parts[$k]);
           if ( $value == 1 ) {
            $highlight=true;
            $fore+=8;
            echo 'Bold';
           } else if ( $value == 0 ) {
            $fore=7;
            $back=0;
            $highlight=false;
            $underline=false;
            $blinking=false;
            $inverted=false;
            $hidden=false;
            echo 'Normal';
           } else if ( $value == 4 ) {
            $underline=!$underline;
           } else if ( $value == 5 ) {
            $blinking=!$blinking;
           } else if ( $value == 7 ) {
            $inverted=!$inverted;
           } else if ( $value == 8 ) {
            $hidden=!$hidden;
           } else
           if ( $value >= 30 && $value < 40 ) {
            $fore=$value-30;
            if ( $highlight===true ) {
             echo ' Highlighting '.$fore .' -> ';
             $fore+=8;
             echo $fore;
            } else echo ' Setting foreground to '.$fore;
           } else if ( $value >= 40 && $value < 50 ) {
            $back=$value-40;
            echo ' Setting background to '.$back;
           }
          }
         }
        break;
       case 'C':
         {
          $number=intval($parts[0]);
          $out[]=array($fore,$back,32,$parts[0]);
//"[color(".$fore.",".$back.",[c(32,".$parts[0].")])]";
         }
        break;
       case 'h': // ignores code 7h
        break;
       case '?': // ignores 'screen mode' code ?#
        break;
      }
      echo "\n";
      var_dump($parts);
     }
    break;
   case 32: $out[]=array($fore,$back,32,1); //"[color(".$fore.",".$back.",%b)]";
    break;
   default: $out[]=array($fore,$back,$o,1); //"[color(".$fore.",".$back.",[c(".$o.")])]";
    break;
  }
 }

$final="";
$total=count($out);
for ( $i=0; $i<$total; $i++ ) {
 if ( is_string( $out[$i] ) ) {
  $final.=$out[$i];
 } else if ( is_array( $out[$i] ) ) {
  $fore=$out[$i][0];
  $back=$out[$i][1];
  if ( $i == $total-1 ) {
   if ( $out[$i][3] == 1 )
   $final.="[color(".$fore.",".$back.",[c(".$out[$i][2].")])]"; else
   $final.="[color(".$fore.",".$back.",[c(".$out[$i][2].','.$out[$i][3].")])]";
  } else if ( !is_array($out[$i+1]) ) {
   if ( $out[$i][3] == 1 )
   $final.="[color(".$fore.",".$back.",[c(".$out[$i][2].")])]"; else
   $final.="[color(".$fore.",".$back.",[c(".$out[$i][2].','.$out[$i][3].")])]";
  } else if ( is_array($out[$i+1]) ) {
   if ( $out[$i][2] != $out[$i+1][2] || $out[$i][1] != $out[$i+1][1] || $out[$i][0] != $out[$i+1][0] ) {
   if ( $out[$i][3] == 1 )
    $final.="[color(".$fore.",".$back.",[c(".$out[$i][2].")])]"; else
    $final.="[color(".$fore.",".$back.",[c(".$out[$i][2].','.$out[$i][3].")])]";
   } else { $out[$i+1][3] += $out[$i][3]; }
  }
 }
}

echo "---\n";
echo $final;
