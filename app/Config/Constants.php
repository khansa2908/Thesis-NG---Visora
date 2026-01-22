<?php

/*
 | --------------------------------------------------------------------
 | App Namespace
 | --------------------------------------------------------------------
 |
 | This defines the default Namespace that is used throughout
 | CodeIgniter to refer to the Application directory. Change
 | this constant to change the namespace that all application
 | classes should use.
 |
 | NOTE: changing this will require manually modifying the
 | existing namespaces of App\* namespaced-classes.
 */
defined('APP_NAMESPACE') || define('APP_NAMESPACE', 'App');

/*
 | --------------------------------------------------------------------------
 | Composer Path
 | --------------------------------------------------------------------------
 |
 | The path that Composer's autoload file is expected to live. By default,
 | the vendor folder is in the Root directory, but you can customize that here.
 */
defined('COMPOSER_PATH') || define('COMPOSER_PATH', ROOTPATH . 'vendor/autoload.php');

/*
 |--------------------------------------------------------------------------
 | Timing Constants
 |--------------------------------------------------------------------------
 |
 | Provide simple ways to work with the myriad of PHP functions that
 | require information to be in seconds.
 */
defined('SECOND') || define('SECOND', 1);
defined('MINUTE') || define('MINUTE', 60);
defined('HOUR')   || define('HOUR', 3600);
defined('DAY')    || define('DAY', 86400);
defined('WEEK')   || define('WEEK', 604800);
defined('MONTH')  || define('MONTH', 2592000);
defined('YEAR')   || define('YEAR', 31536000);
defined('DECADE') || define('DECADE', 315360000);

/*
 | --------------------------------------------------------------------------
 | Exit Status Codes
 | --------------------------------------------------------------------------
 |
 | Used to indicate the conditions under which the script is exit()ing.
 | While there is no universal standard for error codes, there are some
 | broad conventions.  Three such conventions are mentioned below, for
 | those who wish to make use of them.  The CodeIgniter defaults were
 | chosen for the least overlap with these conventions, while still
 | leaving room for others to be defined in future versions and user
 | applications.
 |
 | The three main conventions used for determining exit status codes
 | are as follows:
 |
 |    Standard C/C++ Library (stdlibc):
 |       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
 |       (This link also contains other GNU-specific conventions)
 |    BSD sysexits.h:
 |       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
 |    Bash scripting:
 |       http://tldp.org/LDP/abs/html/exitcodes.html
 |
 */
defined('EXIT_SUCCESS')        || define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR')          || define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG')         || define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE')   || define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS')  || define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') || define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     || define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE')       || define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN')      || define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      || define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code

// USER constant
defined('BATAS_NILAI_KELULUSAN_PROYEK') || define('BATAS_NILAI_KELULUSAN_PROYEK', 50);
defined('BATAS_NILAI_KELULUSAN_SKRIPSI_REGULER') || define('BATAS_NILAI_KELULUSAN_SKRIPSI_REGULER', 65);

defined('STATUS_SKRIPSI_LULUS') || define('STATUS_SKRIPSI_LULUS', 'Lulus');
defined('STATUS_SKRIPSI_TIDAKLULUS') || define('STATUS_SKRIPSI_TIDAKLULUS', 'Tidak Lulus');
defined('STATUS_SKRIPSI_DALAMPENGERJAAN') || define('STATUS_SKRIPSI_DALAMPENGERJAAN', 'Dalam Pengerjaan');

defined('BATAS_TAHUN_AKADEMIK_LAMA') || define('BATAS_TAHUN_AKADEMIK_LAMA', 2022);
defined('BATAS_SEMESTER_LAMA') || define('BATAS_SEMESTER_LAMA', 2);

defined('JENIS_SKRIPSI_REGULER') || define('JENIS_SKRIPSI_REGULER', "SKRIPSI REGULER");
defined('JENIS_SKRIPSI_PROYEK_TIM') || define('JENIS_SKRIPSI_PROYEK_TIM', "PROYEK TIM");
defined('JENIS_SKRIPSI_PROYEK_MANDIRI') || define('JENIS_SKRIPSI_PROYEK_MANDIRI', "PROYEK MANDIRI");

//NILAI CONSTANT
defined('BATAS_BAWAH_NILAI_A') || define("BATAS_BAWAH_NILAI_A", 80);
defined('BATAS_BAWAH_NILAI_AMIN') || define("BATAS_BAWAH_NILAI_AMIN", 76.25);
defined('BATAS_BAWAH_NILAI_AB') || define("BATAS_BAWAH_NILAI_AB", 72.50);
defined('BATAS_BAWAH_NILAI_BPLUS') || define("BATAS_BAWAH_NILAI_BPLUS", 68.75);
defined('BATAS_BAWAH_NILAI_B') || define("BATAS_BAWAH_NILAI_B", 65.00);
defined('BATAS_BAWAH_NILAI_BMIN') || define("BATAS_BAWAH_NILAI_BMIN", 61.25);
defined('BATAS_BAWAH_NILAI_BC') || define("BATAS_BAWAH_NILAI_BC", 57.50);
defined('BATAS_BAWAH_NILAI_CPLUS') || define("BATAS_BAWAH_NILAI_CPLUS", 53.75);
defined('BATAS_BAWAH_NILAI_C') || define("BATAS_BAWAH_NILAI_C", 50.00);
defined('BATAS_BAWAH_NILAI_CMIN') || define("BATAS_BAWAH_NILAI_CMIN", 46.25);
defined('BATAS_BAWAH_NILAI_CD') || define("BATAS_BAWAH_NILAI_CD", 42.50);
defined('BATAS_BAWAH_NILAI_DPLUS') || define("BATAS_BAWAH_NILAI_DPLUS", 38.75);
defined('BATAS_BAWAH_NILAI_D') || define("BATAS_BAWAH_NILAI_D", 35.00);
defined('BATAS_BAWAH_NILAI_E') || define("BATAS_BAWAH_NILAI_E", 00.00);

//BATAS BERAPA KALI BOLEH NGULANG SEMINAR PRASIDANG
defined('BATAS_PENGULANGAN_SEMINAR') || define("BATAS_PENGULANGAN_SEMINAR", 2);

//MODE SEMINAR PROPOSAL
defined('ASINKRONUS') || define("ASINKRONUS", "Asinkronus");
defined('SINKRONUS_DARING') || define("SINKRONUS_DARING", "Sinkronus Daring");
defined('SINKRONUS_LURING') || define("SINKRONUS_LURING", "Sinkronus Luring");

