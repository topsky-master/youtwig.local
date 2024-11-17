<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Xls;

class RC4
{
    // Context
    protected $s = [];

    protected $i = 0;

    protected $j = 0;

    /**
     * RC4 stream decryption/encryption constrcutor.
     *
     * @param string $key Encryption key/passphrase
     */
    public function __construct($key)
    {
        $len = mb_strlen($key);

        for ($this->i = 0; $this->i < 256; ++$this->i) {
            $this->s[$this->i] = $this->i;
        }

        $this->j = 0;
        for ($this->i = 0; $this->i < 256; ++$this->i) {
            $this->j = ($this->j + $this->s[$this->i] + mb_ord($key[$this->i % $len])) % 256;
            $t = $this->s[$this->i];
            $this->s[$this->i] = $this->s[$this->j];
            $this->s[$this->j] = $t;
        }
        $this->i = $this->j = 0;
    }

    /**
     * Symmetric decryption/encryption function.
     *
     * @param string $data Data to encrypt/decrypt
     *
     * @return string
     */
    public function RC4($data)
    {
        $len = mb_strlen($data);
        for ($c = 0; $c < $len; ++$c) {
            $this->i = ($this->i + 1) % 256;
            $this->j = ($this->j + $this->s[$this->i]) % 256;
            $t = $this->s[$this->i];
            $this->s[$this->i] = $this->s[$this->j];
            $this->s[$this->j] = $t;

            $t = ($this->s[$this->i] + $this->s[$this->j]) % 256;

            $data[$c] = mb_chr(mb_ord($data[$c]) ^ $this->s[$t]);
        }

        return $data;
    }
}
