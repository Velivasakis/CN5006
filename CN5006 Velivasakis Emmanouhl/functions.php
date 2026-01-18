<?php
// Συνάρτηση για έλεγχο εγκυρότητας πεδίων
function check_field($field, $regex, $min, $max){
        // Καθαρισμός κενών
        $final_field = isset($field) ? trim($field) : ""; 
        
        // Αν είναι άδειο επιστρέφει false
        if ($final_field === ""){
            return false;
        }

        // Έλεγχος με Regular Expression (Regex)
        if (!preg_match($regex, $final_field)){
            return false;
        }

        // Έλεγχος μήκους
        if (strlen($final_field) < $min || strlen($final_field) > $max){
            return false;
        }

        return true;
    }
?>