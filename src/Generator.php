<?php

namespace Haibrini\Password;

/**
 * Password generator. Generates passwords from wordlist passed
 * as WordListInterface
 */
class Generator
{
    const SYMBOLS = array('!', '@', '#', '$', '%', '&', '-', '=');

    /**
     * Get array of random numbers between 0.0 - 1.0
     * Uses openssl_random_pseudo_bytes as random funciton
     *
     * @param  integer $length array length
     * @return float[] array of random values 0.0 - 1.0
     */
    public static function getStrongRandomArray($length)
    {
        $bytes = openssl_random_pseudo_bytes($length * 2);
        $longs = unpack('S*', $bytes);
        $result = array();
        foreach ($longs as $long) {
            // should check if this division doesn't affects
            // the random distirbution
            $result[] = $long / 0xffff;
        }

        return $result;
    }

    /**
     * Get array of random numbers between 0.0 - 1.0
     * Uses mt_rand as random funciton.
     *
     * @param  integer $length array length
     * @return float[] array of random values 0.0 - 1.0
     */
    public static function getMtRandomArray($length)
    {
        $result = array();
        foreach (range(1, $length) as $counter) {
            $result[] = mt_rand() / mt_getrandmax();
        }

        return $result;
    }

    /**
     * Get array of random numbers between 0.0 - 1.0
     * Uses openssl random generator if avaivable, mt_rand othervise
     *
     * @param  integer $length array length
     * @return float[] array of random values 0.0 - 1.0
     */
    public static function getRandomArray($length)
    {
        if (function_exists('openssl_random_pseudo_bytes')) {
            return self::getStrongRandomArray($length);
        }

        return self::getMtRandomArray($length);
    }

    /**
     * Get array of random numbers between 0 - 9
     * Uses mt_rand as random funciton.
     *
     * @param  integer $length array length
     * @return string string of random digits 0 - 9
     */
    public static function getMtRandomDigits($length)
    {
        $result = array();
        foreach (range(1, $length) as $counter) {
            $result[] = mt_rand(0, 9);
        }

        return implode('', $result);
    }

    /**
     * Get array of random numbers between 0 - 9
     * Uses openssl random generator if avaivable, mt_rand othervise
     *
     * @param  integer $length array length
     * @return string string of random digits 0 - 9
     */
    public static function getRandomDigits($length)
    {
//        if (function_exists('openssl_random_pseudo_bytes')) {
//            return self::getStrongRandomDigitsArray($length);
//        }

        return self::getMtRandomDigits($length);
    }

    /**
     * Get a random symbol separator
     *
     * @return string random separator
     */
    public static function getRandomSeparator()
    {
        return self::SYMBOLS[mt_rand(0, count(self::SYMBOLS) - 1)];
    }

    /**
     * Static function generates transliterated Russian phrase password
     *
     * Example "kater nekiy zabrat dazhe"
     *
     * @param  integer $lenght    password length (number of words). Default - 4
     * @param  string  $separator word separator. Default ' ' (space)
     * @param  int     $uppercase 0=lowercase, 1=UPPERCASE, 2=Capitalize
     *
     * @return string  generated password
     */
    public static function generateRuTranslit($lenght = 4, $uppercase = 0, $digits = 0, $separator = ' ')
    {
        return self::generate(new WordList\RuTranslit(), $lenght, $uppercase, $digits, $separator);
    }

    /**
     * Static function to generate password from wordlists.
     *
     * If array of wordlist is shorter then length,
     * function would iterate from the beginning of array
     *
     * @param  WordListInterface[] | WordListInterface
     *                           $wordLists array of word lists or word list
     * @param  integer $lenght    password length in words. Default - 4
     * @param  int     $uppercase 0=lowercase, 1=UPPERCASE, 2=Capitalize
     * @param  int     $digits    number of digits to be added at the end
     * @param  string  $separator word separator. Default - ' '(space)
     *
     * @return string  generated password
     */
    public static function generate($wordLists, $lenght = 4, $uppercase = 0, $digits = 0, $separator = '')
    {
        if (!is_array($wordLists)) {
            $wordLists = array($wordLists);
        }
        $wordListsLength = count($wordLists);

        $words = array();
        $randomArray = self::getRandomArray($lenght);
        foreach ($randomArray as $index => $random) {
            $wordList = $wordLists[$index % $wordListsLength];
            switch ($uppercase) {
                case 0:
                    $words[] = $wordList->get($random);break;
                case 1:
                    $words[] = strtoupper($wordList->get($random));break;
                case 2:
                    $words[] = ucfirst($wordList->get($random));break;
            }
        }

        if ($digits > 0)
            $words[] = self::getRandomDigits($digits);
        if (empty($separator))
            $separator = self::getRandomSeparator();

        return join($separator, $words);
    }

    /**
     * Static function generates English phrase password.
     *
     * Example "limit bend realm square"
     *
     * @param  integer $lenght    password length (number of words). Default - 4
     * @param  int     $uppercase 0=lowercase, 1=UPPERCASE, 2=Capitalize
     * @param  int     $digits    number of digits to be added at the end
     * @param  string  $separator word separator. Default ' ' (space)
     *
     * @return string  generated password
     */
    public static function generateEn($lenght = 4, $uppercase = 0, $digits = 0, $separator = '')
    {
        return self::generate(new WordList\En(), $lenght, $uppercase, $digits, $separator);
    }


    /**
     * Static function generates German phrase password.
     *
     * Example "laut welt ganze liter"
     *
     * @param  integer $lenght    password length (number of words). Default - 4
     * @param  int     $uppercase 0=lowercase, 1=UPPERCASE, 2=Capitalize
     * @param  int     $digits    number of digits to be added at the end
     * @param  string  $separator word separator. Default ' ' (space)
     *
     * @return string  generated password
     */
    public static function generateDe($lenght = 4, $uppercase = 0, $digits = 0, $separator = '')
    {
        return self::generate(new WordList\De(), $lenght, $uppercase, $digits, $separator);
    }
}