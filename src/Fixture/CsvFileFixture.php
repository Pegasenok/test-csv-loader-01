<?php


namespace App\Fixture;


class CsvFileFixture
{
    private int $shift = 0;

    public function generate(string $fileName, int $rowCount)
    {
        $file = fopen($fileName, 'w');
        for ($i = 1; $i <= $rowCount; $i++) {
            $id = $i + $this->getShift();
            $name = $this->getRandomString(rand(3,10));
            $email = $this->getRandomString(rand(1,7)) . '@' . $this->getRandomString(rand(4,5)) . '.com';
            $cur = 'usd';
            $val = random_int(0, 50000) + round(1 / rand(1, 100),2);
            fwrite($file, sprintf("%s,%s,%s,%s,%s\n", $id, $name, $email, $cur, $val));
        }
        fclose($file);
    }

    /**
     * @return int
     */
    public function getShift(): int
    {
        return $this->shift;
    }

    /**
     * @param int $shift
     */
    public function setShift(int $shift): void
    {
        $this->shift = $shift;
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function getRandomString($length): string
    {
        return bin2hex(random_bytes($length));
    }
}