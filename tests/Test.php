<?php

use adrianschubek\Store\Store;
use PHPUnit\Framework\TestCase;

class Test extends TestCase
{
    public function test_1()
    {
        $s = new Store();
        $y = store();

        self::assertInstanceOf(Store::class, $s);
        self::assertInstanceOf(Store::class, $y);
    }

    public function test_2()
    {
        $s = store(["Düsseldorf", "Berlin", "Köln"]);

        self::assertSame(["Düsseldorf", "Berlin", "Köln"], $s->all());
    }

    public function test_3()
    {
        $s = store([null, "Düsseldorf", "Berlin", null, "Köln"]);

        self::assertSame(["Düsseldorf", "Berlin", "Köln"],
            $s->strip()
                ->all()
        );
    }

    public function test_4()
    {
        $s = store([null, "düsseldorf", "berlin", null, "köln"]);

        self::assertSame(["DÜSSELDORF", "BERLIN", "KÖLN"],
            $s->each(fn($val) => mb_strtoupper($val))
                ->strip()
                ->all()
        );
    }

    public function test_5()
    {
        $s = store([null, "Düsseldorf", "Berlin", null, "Köln"]);

        self::assertSame(["DÜSSELDORF", "KÖLN"],
            $s->each(fn($val) => mb_strtoupper($val))
                ->filter(fn($val) => $val !== "BERLIN")
                ->strip()
                ->all()
        );
    }

    public function test_6()
    {
        $s = store(["Düsseldorf", "Berlin", "Köln"]);

        self::assertSame(["Düsseldorf", "Köln"],
            $s->exclude(fn($val) => $val === "Berlin")
                ->all()
        );
    }

    public function test_7()
    {
        $s = store(["Düsseldorf", "Berlin", "Köln"]);

        self::assertSame(["Berlin"],
            $s->include(fn($val) => $val === "Berlin")
                ->all()
        );

        self::assertSame(["Berlin"],
            $s->include(fn($val) => $val === "Berlin")
                ->all()
        );

        $b = store(["DE" => "Berlin", "FR" => "Paris"]);
        self::assertSame(["Berlin"],
            $s->include(fn($val) => $val === "Berlin")
                ->all()
        );

        $s = store([null, "düsseldorf", "berlin", null, "köln"]);

        self::assertSame(["", "DÜSSELDORF", "BERLIN", "", "KÖLN"],
            $s->toUpper()
                ->all()
        );

        Store::mixin("dd", fn(Store $store) => dd($store->all()));

//        $b->map(fn($key, $val) => dump($key, $val));
//        $b->dd();



    }

    public function test_8()
    {
        $s = store(["Düsseldorf", null, "Berlin", "Köln"]);

        Store::mixin("removeBerlin", function (Store &$store, bool $strip, int $num) {
            $store = $store->exclude(fn($val) => $val === "Berlin");
            if ($strip === true) {
                $store = $store->strip();
            }
        });

        self::assertSame(["Düsseldorf", "Köln"],
            $s->removeBerlin(true, 5)
                ->all()
        );
    }


}
