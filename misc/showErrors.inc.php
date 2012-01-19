<?php
// This file is a part of the MyMirror Project, a PHP University Project.
//
// Copyright (c) 2012 Theodore R.Smith (theodore@phpexperts.pro)
// DSA-1024 Fingerprint: 10A0 6372 9092 85A2 BB7F  907B CB8B 654B E33B F1ED
// Provided by the PHP University (www.phpu.cc)
//
// This file is dually licensed under the terms of the following licenses:
// * Primary License: OSSAL - Open Source Software Alliance License
//   * Key points:
//       5.Redistributions of source code in any non-textual form (i.e.
//          binary or object form, etc.) must not be linked to software that is
//          released with a license that requires disclosure of source code
//          (ex: the GPL).
//       6.Redistributions of source code must be licensed under more than one
//          license and must not have the terms of the OSSAL removed.
//   * See http://repo.phpexperts.pro/license-ossal.html for complete details.
//
// * Secondary License: Creative Commons Attribution License v3.0
//   * Key Points:
//       * You are free:
//           * to copy, distribute, display, and perform the work
//           * to make non-commercial or commercial use of the work in its original form
//       * Under the following conditions:
//           * Attribution. You must give the original author credit. You must retain all
//             Copyright notices and you must include the sentence, "Based upon work from the
//             the PHP University (www.phpu.cc).", wherever you list contributors and authors.
//   * See http://creativecommons.org/licenses/by/3.0/ for complete details.

function showErrorMessage(Exception $e)
{
?>
    <div class="error_message">
        <h2>Oops! An error has occured:</h2>
        <p>Error: <?php echo $e->getMessage(); ?></p>
    </div>
<?php
}

