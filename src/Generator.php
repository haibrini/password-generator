<?php

namespace Barzo\Password;

/**
 * Password generator. Generates passwords from wordlist passed
 * as WordListInterface
 */
class Generator
{
  
    /**
  	 * Static function to generate password
  	 * 
  	 * @param  WordListInterface $wordList  word list
  	 * @param  integer           $lenght    password length (number of words). Default - 4
  	 * @param  string            $separator word separator. Default ' ' (space)
  	 * @return string                       generated password
  	 */
    public static function generate(WordListInterface $wordList, $lenght = 4, $separator = ' ')
    {
        $words = array();
        foreach (range(1, $lenght) as $number) {
            $random = mt_rand() / mt_getrandmax();
            $words[] = $wordList->get($random);
        }
        
        return join($separator, $words);
    }
}
