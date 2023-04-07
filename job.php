<?php
/*
  how dis workes?
  well firste it umm check if any job to be done and check if job is not done
  then do task that is oldest that isnt done... then do others from oldest to newest
  
  since i think this is good idea..
*/
if(isset($_SERVER['REMOTE_ADDR'])) {
die(header("Location: https://www.upwork.com/freelance-jobs/online/"));
}
define("INTERVAL", 5 );

function runIt($i) {
    echo "task-".$i.": \e[37mStarting job...\n\e[0m";
    sleep(1.5);
    echo "task-".$i.": \e[32mRunning ffmpeg...\n\n\e[0m";
    sleep(5.5);
    echo "task-".$i.": \e[32mDone!\n\e[0m";
}

function checkForStopFlag() {
    return false;
}

function start() {
    $i = 1;
    $active = true;
    $nextTime   = microtime(true) + INTERVAL;

    while($active) {
        usleep(1000);

        if (microtime(true) >= $nextTime) {
            runIt($i);
            $nextTime = microtime(true) + INTERVAL;
            $i++;
        }
    }
}

start();