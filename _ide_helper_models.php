<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property int $session_id
 * @property int $student_id
 * @property \Illuminate\Support\Carbon $submission_time
 * @property string $status
 * @property string $learning_type
 * @property string $photo_path
 * @property string $location_maps
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AttendanceSession $session
 * @property-read \App\Models\StudentProfile $student
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceRecord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceRecord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceRecord query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceRecord whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceRecord whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceRecord whereLearningType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceRecord whereLocationMaps($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceRecord wherePhotoPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceRecord whereSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceRecord whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceRecord whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceRecord whereSubmissionTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceRecord whereUpdatedAt($value)
 */
	class AttendanceRecord extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $course_id
 * @property \Illuminate\Support\Carbon $session_date
 * @property \Illuminate\Support\Carbon $start_time
 * @property \Illuminate\Support\Carbon $end_time
 * @property string $session_type
 * @property int|null $location_id
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Course $course
 * @property-read \App\Models\Location|null $location
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AttendanceRecord> $records
 * @property-read int|null $records_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceSession newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceSession newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceSession query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceSession whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceSession whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceSession whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceSession whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceSession whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceSession whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceSession whereSessionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceSession whereSessionType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceSession whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceSession whereUpdatedAt($value)
 */
	class AttendanceSession extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $course_code
 * @property string $course_name
 * @property string $course_time
 * @property int $lecturer_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\LecturerProfile $lecturer
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AttendanceSession> $sessions
 * @property-read int|null $sessions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StudentProfile> $students
 * @property-read int|null $students_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereCourseCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereCourseName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereCourseTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereLecturerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereUpdatedAt($value)
 */
	class Course extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nid
 * @property string $full_name
 * @property string|null $phone_number
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Course> $courses
 * @property-read int|null $courses_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LecturerProfile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LecturerProfile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LecturerProfile query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LecturerProfile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LecturerProfile whereFullName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LecturerProfile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LecturerProfile whereNid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LecturerProfile wherePhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LecturerProfile whereUpdatedAt($value)
 */
	class LecturerProfile extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $location_name
 * @property string $latitude
 * @property string $longitude
 * @property int $radius_meters
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AttendanceSession> $sessions
 * @property-read int|null $sessions_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location whereLocationName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location whereRadiusMeters($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location whereUpdatedAt($value)
 */
	class Location extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $npm
 * @property string $full_name
 * @property string|null $phone_number
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AttendanceRecord> $attendanceRecords
 * @property-read int|null $attendance_records_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Course> $courses
 * @property-read int|null $courses_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentProfile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentProfile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentProfile query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentProfile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentProfile whereFullName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentProfile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentProfile whereNpm($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentProfile wherePhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentProfile whereUpdatedAt($value)
 */
	class StudentProfile extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $email_verified_at
 * @property string $password
 * @property string $role
 * @property string|null $profile_type
 * @property int|null $profile_id
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $lecturer_profile
 * @property-read mixed $student_profile
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent|null $profile
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereProfileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereProfileType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

