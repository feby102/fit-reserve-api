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
 * @property int $vendor_id
 * @property int $academy_type_id
 * @property string $name
 * @property string $location
 * @property int $is_active
 * @property numeric $price_per_hour
 * @property string|null $image
 * @property int $is_verified
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Booking> $bookings
 * @property-read int|null $bookings_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Challenge> $challenge
 * @property-read int|null $challenge_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Facility> $facilities
 * @property-read int|null $facilities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AcademyPackage> $packages
 * @property-read int|null $packages_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AcademyPlan> $plans
 * @property-read int|null $plans_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PrivateCoach> $privateCoich
 * @property-read int|null $private_coich_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Review> $reviews
 * @property-read int|null $reviews_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AcademyService> $services
 * @property-read int|null $services_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AcademyStudent> $students
 * @property-read int|null $students_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AcademySubscription> $subscriptions
 * @property-read int|null $subscriptions_count
 * @property-read \App\Models\AcademyType|null $type
 * @property-read \App\Models\Vendor $vendor
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Academy newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Academy newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Academy query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Academy whereAcademyTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Academy whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Academy whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Academy whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Academy whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Academy whereIsVerified($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Academy whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Academy whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Academy wherePricePerHour($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Academy whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Academy whereVendorId($value)
 */
	class Academy extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property int $hours
 * @property numeric $price
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Booking> $bookings
 * @property-read int|null $bookings_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyPackage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyPackage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyPackage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyPackage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyPackage whereHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyPackage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyPackage whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyPackage wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyPackage whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyPackage whereUpdatedAt($value)
 */
	class AcademyPackage extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $academy_id
 * @property numeric $price
 * @property string $name
 * @property string $type
 * @property int $max_students
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Academy $academy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Booking> $bookings
 * @property-read int|null $bookings_count
 * @property-read AcademyPlan|null $plan
 * @property-read \App\Models\Vendor|null $vendor
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyPlan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyPlan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyPlan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyPlan whereAcademyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyPlan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyPlan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyPlan whereMaxStudents($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyPlan whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyPlan wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyPlan whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyPlan whereUpdatedAt($value)
 */
	class AcademyPlan extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $academy_id
 * @property int $rating
 * @property string $review
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyReview newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyReview newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyReview query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyReview whereAcademyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyReview whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyReview whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyReview whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyReview whereReview($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyReview whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyReview whereUserId($value)
 */
	class AcademyReview extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $academy_id
 * @property string $name
 * @property numeric $price
 * @property int $duration
 * @property int $max_number
 * @property int $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Academy $academy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Booking> $bookings
 * @property-read int|null $bookings_count
 * @property-read \App\Models\AcademyPlan|null $plan
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Review> $reviews
 * @property-read int|null $reviews_count
 * @property-read \App\Models\User|null $user
 * @property-read \App\Models\Vendor|null $vendor
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyService newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyService newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyService query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyService whereAcademyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyService whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyService whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyService whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyService whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyService whereMaxNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyService whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyService wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyService whereUpdatedAt($value)
 */
	class AcademyService extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $academy_id
 * @property int $user_id
 * @property int $academy_plan_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyStudent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyStudent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyStudent query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyStudent whereAcademyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyStudent whereAcademyPlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyStudent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyStudent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyStudent whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyStudent whereUserId($value)
 */
	class AcademyStudent extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $academy_plan_id
 * @property int $user_id
 * @property string $start_date
 * @property string $end_date
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AcademyPlan $plan
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademySubscription newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademySubscription newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademySubscription query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademySubscription whereAcademyPlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademySubscription whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademySubscription whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademySubscription whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademySubscription whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademySubscription whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademySubscription whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademySubscription whereUserId($value)
 */
	class AcademySubscription extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyType query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyType whereUpdatedAt($value)
 */
	class AcademyType extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $title
 * @property string $image
 * @property string|null $link
 * @property int $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Banner newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Banner newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Banner query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Banner whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Banner whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Banner whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Banner whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Banner whereLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Banner whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Banner whereUpdatedAt($value)
 */
	class Banner extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $bookable_type
 * @property int $bookable_id
 * @property \Illuminate\Support\Carbon $start_time
 * @property \Illuminate\Support\Carbon $end_time
 * @property int $hours
 * @property numeric $total_price
 * @property string $payment_method
 * @property string|null $rejection_reason
 * @property string $status
 * @property string|null $full_name
 * @property int|null $age
 * @property string|null $parent_id_card
 * @property string|null $personal_photo
 * @property string|null $coupon_code
 * @property numeric $discount_amount
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $bookable
 * @property-read \App\Models\Coupon|null $coupon
 * @property-read \App\Models\Facility|null $facility
 * @property-read \App\Models\StadiumPackage|null $package
 * @property-read \App\Models\AcademyService|null $services
 * @property-read \App\Models\Stadium|null $stadium
 * @property-read \App\Models\Studio|null $studio
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereAge($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereBookableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereBookableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereCouponCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereDiscountAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereFullName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereParentIdCard($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking wherePersonalPhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereRejectionReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereTotalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereUserId($value)
 */
	class Booking extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $product_id
 * @property int $quantity
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Product $product
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart whereUserId($value)
 */
	class Cart extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $image
 * @property int $vendor_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Product> $products
 * @property-read int|null $products_count
 * @property-read \App\Models\Vendor $vendor
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereVendorId($value)
 */
	class Category extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $title
 * @property int $vendor_id
 * @property int $academy_id
 * @property int $max_players
 * @property numeric $price
 * @property int $duration
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Stadium $academy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ChallengeParticipant> $participants
 * @property-read int|null $participants_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PlayerRating> $playerRatings
 * @property-read int|null $player_ratings_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Review> $reviews
 * @property-read int|null $reviews_count
 * @property-read \App\Models\Vendor $vendor
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Challenge newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Challenge newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Challenge query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Challenge whereAcademyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Challenge whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Challenge whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Challenge whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Challenge whereMaxPlayers($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Challenge wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Challenge whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Challenge whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Challenge whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Challenge whereVendorId($value)
 */
	class Challenge extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $challenge_id
 * @property int $user_id
 * @property string $status
 * @property int $is_banned
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Challenge $challenge
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChallengeParticipant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChallengeParticipant newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChallengeParticipant query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChallengeParticipant whereChallengeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChallengeParticipant whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChallengeParticipant whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChallengeParticipant whereIsBanned($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChallengeParticipant whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChallengeParticipant whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChallengeParticipant whereUserId($value)
 */
	class ChallengeParticipant extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $private_coach_id
 * @property string $start_time
 * @property string $end_time
 * @property int $hours
 * @property numeric $total_price
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\PrivateCoach $coach
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CoachBooking newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CoachBooking newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CoachBooking query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CoachBooking whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CoachBooking whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CoachBooking whereHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CoachBooking whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CoachBooking wherePrivateCoachId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CoachBooking whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CoachBooking whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CoachBooking whereTotalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CoachBooking whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CoachBooking whereUserId($value)
 */
	class CoachBooking extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $private_coach_id
 * @property string $location
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CoachLocation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CoachLocation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CoachLocation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CoachLocation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CoachLocation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CoachLocation whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CoachLocation wherePrivateCoachId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CoachLocation whereUpdatedAt($value)
 */
	class CoachLocation extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $private_coach_id
 * @property string $name
 * @property numeric $price
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Academy|null $academy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Booking> $bookings
 * @property-read int|null $bookings_count
 * @property-read \App\Models\Gym|null $gym
 * @property-read \App\Models\GymPlan|null $plan
 * @property-read \App\Models\PrivateCoach $privateCoach
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CoachService newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CoachService newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CoachService query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CoachService whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CoachService whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CoachService whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CoachService wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CoachService wherePrivateCoachId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CoachService whereUpdatedAt($value)
 */
	class CoachService extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_one_id
 * @property int $user_two_id
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Message> $messages
 * @property-read int|null $messages_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $participants
 * @property-read int|null $participants_count
 * @property-read \App\Models\User $userOne
 * @property-read \App\Models\User $userTwo
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Conversation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Conversation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Conversation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Conversation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Conversation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Conversation whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Conversation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Conversation whereUserOneId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Conversation whereUserTwoId($value)
 */
	class Conversation extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $vendor_id
 * @property string $code
 * @property string $type
 * @property numeric|null $value
 * @property int|null $max_usage
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property int $academy_service_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Product> $products
 * @property-read int|null $products_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AcademyService> $services
 * @property-read int|null $services_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon whereAcademyServiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon whereMaxUsage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon whereValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon whereVendorId($value)
 */
	class Coupon extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $vendor_id
 * @property string $name
 * @property string $duration
 * @property numeric $price
 * @property int $is_active
 * @property string|null $serviceable_type
 * @property int|null $serviceable_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Booking> $bookings
 * @property-read int|null $bookings_count
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $facilityeable
 * @property-read \App\Models\Vendor $vendor
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facility newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facility newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facility query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facility whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facility whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facility whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facility whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facility whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facility wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facility whereServiceableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facility whereServiceableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facility whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facility whereVendorId($value)
 */
	class Facility extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property int $hours
 * @property numeric $price
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Booking> $bookings
 * @property-read int|null $bookings_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FacilityPackage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FacilityPackage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FacilityPackage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FacilityPackage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FacilityPackage whereHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FacilityPackage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FacilityPackage whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FacilityPackage wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FacilityPackage whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FacilityPackage whereUpdatedAt($value)
 */
	class FacilityPackage extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $vendor_id
 * @property string $name
 * @property string $type
 * @property string $location
 * @property string|null $description
 * @property string|null $image
 * @property int $is_verified
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Booking> $bookings
 * @property-read int|null $bookings_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Facility> $facilities
 * @property-read int|null $facilities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GymPackage> $packages
 * @property-read int|null $packages_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GymPlan> $plans
 * @property-read int|null $plans_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Review> $reviews
 * @property-read int|null $reviews_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GymSchedule> $schedules
 * @property-read int|null $schedules_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AcademyService> $services
 * @property-read int|null $services_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GymSubscription> $subscriptions
 * @property-read int|null $subscriptions_count
 * @property-read \App\Models\Vendor $vendor
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gym newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gym newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gym query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gym whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gym whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gym whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gym whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gym whereIsVerified($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gym whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gym whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gym whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gym whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gym whereVendorId($value)
 */
	class Gym extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Booking> $bookings
 * @property-read int|null $bookings_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GymPackage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GymPackage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GymPackage query()
 */
	class GymPackage extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $gym_id
 * @property string $name
 * @property int $hours_per_day
 * @property numeric $price
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Booking> $bookings
 * @property-read int|null $bookings_count
 * @property-read \App\Models\Gym $gym
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GymSubscription> $subscriptions
 * @property-read int|null $subscriptions_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GymPlan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GymPlan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GymPlan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GymPlan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GymPlan whereGymId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GymPlan whereHoursPerDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GymPlan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GymPlan whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GymPlan wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GymPlan whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GymPlan whereUpdatedAt($value)
 */
	class GymPlan extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $gym_id
 * @property string $day
 * @property string $start_time
 * @property string $end_time
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GymSchedule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GymSchedule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GymSchedule query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GymSchedule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GymSchedule whereDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GymSchedule whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GymSchedule whereGymId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GymSchedule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GymSchedule whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GymSchedule whereUpdatedAt($value)
 */
	class GymSchedule extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $gym_plan_id
 * @property int $user_id
 * @property string $start_date
 * @property string $end_date
 * @property string $status
 * @property int $auto_renew
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GymSubscription newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GymSubscription newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GymSubscription query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GymSubscription whereAutoRenew($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GymSubscription whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GymSubscription whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GymSubscription whereGymPlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GymSubscription whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GymSubscription whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GymSubscription whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GymSubscription whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GymSubscription whereUserId($value)
 */
	class GymSubscription extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $points
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoyaltyPoint newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoyaltyPoint newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoyaltyPoint query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoyaltyPoint whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoyaltyPoint whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoyaltyPoint wherePoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoyaltyPoint whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoyaltyPoint whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoyaltyPoint whereUserId($value)
 */
	class LoyaltyPoint extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $conversation_id
 * @property int $sender_id
 * @property int $receiver_id
 * @property string|null $message
 * @property string|null $file_path
 * @property string $type
 * @property int $is_flagged
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Conversation $conversation
 * @property-read \App\Models\User $receiver
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Report> $reports
 * @property-read int|null $reports_count
 * @property-read \App\Models\User $sender
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereConversationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereIsFlagged($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereReceiverId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereSenderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereUpdatedAt($value)
 */
	class Message extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $message_id
 * @property int $user_id
 * @property string $reason
 * @property int $reported_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Message $message
 * @property-read \App\Models\User $reporter
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MessageReport newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MessageReport newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MessageReport query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MessageReport whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MessageReport whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MessageReport whereMessageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MessageReport whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MessageReport whereReportedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MessageReport whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MessageReport whereUserId($value)
 */
	class MessageReport extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property string $message
 * @property int $is_read
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereIsRead($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereUserId($value)
 */
	class Notification extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property numeric $total_price
 * @property string $status
 * @property string|null $payment_method
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OrderItem> $items
 * @property-read int|null $items_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereTotalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereUserId($value)
 */
	class Order extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $order_id
 * @property int $product_id
 * @property numeric $quantity
 * @property numeric $price
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Order $order
 * @property-read \App\Models\Product $product
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereUpdatedAt($value)
 */
	class OrderItem extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $title
 * @property string $content
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereUpdatedAt($value)
 */
	class Page extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $challenge_id
 * @property int $evaluator_id
 * @property int $rated_player_id
 * @property int $rating
 * @property string|null $comment
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Challenge $challenge
 * @property-read \App\Models\User $evaluator
 * @property-read \App\Models\User $ratedPlayer
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlayerRating newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlayerRating newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlayerRating query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlayerRating whereChallengeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlayerRating whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlayerRating whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlayerRating whereEvaluatorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlayerRating whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlayerRating whereRatedPlayerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlayerRating whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlayerRating whereUpdatedAt($value)
 */
	class PlayerRating extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $academy_id
 * @property string $name
 * @property string $sport
 * @property numeric $price_per_hour
 * @property string|null $bio
 * @property string|null $image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Academy $academy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Booking> $bookings
 * @property-read int|null $bookings_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Facility> $facilities
 * @property-read int|null $facilities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CoachLocation> $locations
 * @property-read int|null $locations_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PrivateCoachPackage> $packages
 * @property-read int|null $packages_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Review> $reviews
 * @property-read int|null $reviews_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CoachService> $services
 * @property-read int|null $services_count
 * @property-read \App\Models\Vendor|null $vendor
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrivateCoach newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrivateCoach newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrivateCoach query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrivateCoach whereAcademyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrivateCoach whereBio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrivateCoach whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrivateCoach whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrivateCoach whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrivateCoach whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrivateCoach wherePricePerHour($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrivateCoach whereSport($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrivateCoach whereUpdatedAt($value)
 */
	class PrivateCoach extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property int $hours
 * @property numeric $price
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Booking> $bookings
 * @property-read int|null $bookings_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Review> $reviews
 * @property-read int|null $reviews_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrivateCoachPackage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrivateCoachPackage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrivateCoachPackage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrivateCoachPackage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrivateCoachPackage whereHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrivateCoachPackage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrivateCoachPackage whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrivateCoachPackage wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrivateCoachPackage whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrivateCoachPackage whereUpdatedAt($value)
 */
	class PrivateCoachPackage extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property numeric $price
 * @property numeric $discount
 * @property string|null $image
 * @property string|null $video
 * @property int $category_id
 * @property int $store_id
 * @property int|null $vendor_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Category $category
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OrderItem> $orderItems
 * @property-read int|null $order_items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Review> $reviews
 * @property-read int|null $reviews_count
 * @property-read \App\Models\Store $store
 * @property-read \App\Models\Vendor|null $vendor
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereVendorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereVideo($value)
 */
	class Product extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $points
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ranking newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ranking newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ranking query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ranking whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ranking whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ranking wherePoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ranking whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ranking whereUserId($value)
 */
	class Ranking extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $referrer_id
 * @property int $referred_id
 * @property numeric $reward
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $referred
 * @property-read \App\Models\User $referrer
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Referral newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Referral newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Referral query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Referral whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Referral whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Referral whereReferredId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Referral whereReferrerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Referral whereReward($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Referral whereUpdatedAt($value)
 */
	class Referral extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $vendor_id
 * @property string $type
 * @property numeric $total_profit
 * @property int $total_bookings
 * @property string $report_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Vendor $vendor
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereReportDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereTotalBookings($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereTotalProfit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereVendorId($value)
 */
	class Report extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $reviewable_type
 * @property int $reviewable_id
 * @property float $rating
 * @property string|null $comment
 * @property int $is_hidden
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $reviewable
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereIsHidden($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereReviewableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereReviewableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereUserId($value)
 */
	class Review extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property numeric|null $commission_rate
 * @property string|null $cancellation_policy
 * @property int $is_store_enabled
 * @property int $is_challenges_enabled
 * @property int $is_videos_enabled
 * @property string|null $terms
 * @property string|null $privacy_policy
 * @property string|null $about_us
 * @property string|null $banner
 * @property numeric $total_admin_commissions
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereAboutUs($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereBanner($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereCancellationPolicy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereIsChallengesEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereIsStoreEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereIsVideosEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting wherePrivacyPolicy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereTerms($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereTotalAdminCommissions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereUpdatedAt($value)
 */
	class Setting extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $vendor_id
 * @property string $name
 * @property string $city
 * @property string $address
 * @property numeric $price_per_hour
 * @property string|null $description
 * @property string $status
 * @property string|null $image
 * @property int $is_verified
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Booking> $bookings
 * @property-read int|null $bookings_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Facility> $facilities
 * @property-read int|null $facilities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StadiumPackage> $packages
 * @property-read int|null $packages_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Review> $reviews
 * @property-read int|null $reviews_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StadiumSchedule> $schedules
 * @property-read int|null $schedules_count
 * @property-read \App\Models\Vendor $vendor
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stadium newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stadium newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stadium query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stadium whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stadium whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stadium whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stadium whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stadium whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stadium whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stadium whereIsVerified($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stadium whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stadium wherePricePerHour($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stadium whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stadium whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stadium whereVendorId($value)
 */
	class Stadium extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property int $hours
 * @property numeric $price
 * @property string $type
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Booking> $bookings
 * @property-read int|null $bookings_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StadiumPackage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StadiumPackage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StadiumPackage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StadiumPackage whereHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StadiumPackage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StadiumPackage whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StadiumPackage wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StadiumPackage whereType($value)
 */
	class StadiumPackage extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $stadium_id
 * @property string $start_time
 * @property string $end_time
 * @property string $day
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Stadium $stadium
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StadiumSchedule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StadiumSchedule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StadiumSchedule query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StadiumSchedule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StadiumSchedule whereDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StadiumSchedule whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StadiumSchedule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StadiumSchedule whereStadiumId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StadiumSchedule whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StadiumSchedule whereUpdatedAt($value)
 */
	class StadiumSchedule extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $start_date
 * @property string $end_date
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StadiumSubscription newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StadiumSubscription newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StadiumSubscription query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StadiumSubscription whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StadiumSubscription whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StadiumSubscription whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StadiumSubscription whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StadiumSubscription whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StadiumSubscription whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StadiumSubscription whereUserId($value)
 */
	class StadiumSubscription extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $vendor_id
 * @property string $name
 * @property string|null $description
 * @property string|null $logo
 * @property int $is_active
 * @property string|null $image
 * @property int $is_verified
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Category> $categories
 * @property-read int|null $categories_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Product> $products
 * @property-read int|null $products_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Review> $reviews
 * @property-read int|null $reviews_count
 * @property-read \App\Models\Vendor $vendor
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store whereIsVerified($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store whereVendorId($value)
 */
	class Store extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $location
 * @property string|null $description
 * @property numeric $price_per_session
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Booking> $booking
 * @property-read int|null $booking_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Facility> $facilities
 * @property-read int|null $facilities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StudioPackage> $packages
 * @property-read int|null $packages_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Studio newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Studio newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Studio query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Studio whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Studio whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Studio whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Studio whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Studio whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Studio wherePricePerSession($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Studio whereUpdatedAt($value)
 */
	class Studio extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property int $hours
 * @property numeric $price
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Booking> $bookings
 * @property-read int|null $bookings_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudioPackage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudioPackage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudioPackage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudioPackage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudioPackage whereHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudioPackage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudioPackage whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudioPackage wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudioPackage whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudioPackage whereUpdatedAt($value)
 */
	class StudioPackage extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $phone
 * @property string $role
 * @property string|null $city
 * @property string|null $area
 * @property string $email
 * @property string $password
 * @property string|null $my_referral_code
 * @property int|null $referred_by
 * @property numeric $wallet_balance
 * @property string|null $profile_image
 * @property int $is_active
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AcademySubscription> $academySubscriptions
 * @property-read int|null $academy_subscriptions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Booking> $bookings
 * @property-read int|null $bookings_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Challenge> $challenges
 * @property-read int|null $challenges_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $conversations
 * @property-read int|null $conversations_count
 * @property-read mixed $average_rating
 * @property-read mixed $total_points
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoyaltyPoint> $loyaltyPoints
 * @property-read int|null $loyalty_points_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Message> $messages
 * @property-read int|null $messages_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Notification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CoachBooking> $privateCoachBookings
 * @property-read int|null $private_coach_bookings_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PlayerRating> $ratingsGiven
 * @property-read int|null $ratings_given_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PlayerRating> $ratingsReceived
 * @property-read int|null $ratings_received_count
 * @property-read User|null $referrer
 * @property-read \App\Models\Review|null $review
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Stadium> $stadiums
 * @property-read int|null $stadiums_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @property-read \App\Models\Vendor|null $vendor
 * @property-read \App\Models\Wallet|null $wallet
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereArea($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereMyReferralCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereProfileImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereReferredBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereWalletBalance($value)
 */
	class User extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $phone
 * @property string $city
 * @property string|null $area
 * @property string $password
 * @property numeric $balance
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Academy> $academies
 * @property-read int|null $academies_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Gym> $gyms
 * @property-read int|null $gyms_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Product> $products
 * @property-read int|null $products_count
 * @property-read \App\Models\Store|null $store
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereArea($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereUpdatedAt($value)
 */
	class Vendor extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property string $url
 * @property string $type
 * @property int|null $user_id
 * @property int|null $academy_id
 * @property int|null $coach_id
 * @property int $views
 * @property int $likes
 * @property int $dislikes
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Academy|null $academy
 * @property-read \App\Models\PrivateCoach|null $coach
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\VideoReport> $reports
 * @property-read int|null $reports_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Video newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Video newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Video query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Video whereAcademyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Video whereCoachId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Video whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Video whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Video whereDislikes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Video whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Video whereLikes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Video whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Video whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Video whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Video whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Video whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Video whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Video whereViews($value)
 */
	class Video extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $video_id
 * @property int $user_id
 * @property string $reason
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @property-read \App\Models\Video $video
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VideoReport newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VideoReport newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VideoReport query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VideoReport whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VideoReport whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VideoReport whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VideoReport whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VideoReport whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VideoReport whereVideoId($value)
 */
	class VideoReport extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property numeric $balance
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\WalletTransaction> $transactions
 * @property-read int|null $transactions_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wallet newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wallet newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wallet query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wallet whereBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wallet whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wallet whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wallet whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wallet whereUserId($value)
 */
	class Wallet extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $wallet_id
 * @property string $type
 * @property numeric $amount
 * @property string|null $description
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Wallet $wallet
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction whereWalletId($value)
 */
	class WalletTransaction extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Product> $products
 * @property-read int|null $products_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|coupon_product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|coupon_product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|coupon_product query()
 */
	class coupon_product extends \Eloquent {}
}

