<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class ReportTitle extends Enum
{
    const WorkDone = "Công việc đã thực hiện";
    const ExpectedWork = "Công việc dự kiến";
    const Request = "Kiến nghị";
}
