<?php

namespace Minuz\SkolieAPI\tools;

class Parser
{
    public static function HydrateNulls(array &$data, mixed $filling): void
    {
        $filler = function ($item) use ($filling) {
            return is_null($item) ? $filling : $item;
        };

        $data = array_map($filler, $data);
        return;
    }



    public static function HaveValues(array $data, array $checklist): bool
    {
        foreach ($checklist as $checklist_item) {
            if (is_null($data[$checklist_item]) || $data[$checklist_item] == "") {
                return false;
            }
        }

        return true;
    }



    public static function HaveNullVaLues(array $data): bool
    {
        foreach ($data as $item) {
            if ($item == null) {
                return true;
            }
        }

        return false;
    }
}
