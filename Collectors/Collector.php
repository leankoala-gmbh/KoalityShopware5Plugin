<?php

interface KoalityCollector_Collector
{
    /**
     * Return true if the collected data validates against the given rule
     */
    public function validate();
}
