<?php

namespace Haibrini\Password;

/**
 * Interface for list containing words
 */
interface WordListInterface
{
    /**
     * Get word form word list
     *
     * @param  float  $random random position of word in list
     *                        float from 0 to 1
     * @return string word
     */
    public function get($random);
}
