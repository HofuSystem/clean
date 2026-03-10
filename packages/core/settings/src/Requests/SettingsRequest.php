<?php

namespace Core\Settings\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class SettingsRequest extends FormRequest
{
	use ApiResponse;

	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return true;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			"name_en" => ['required', 'string'],
			"name_ar" => ['required', 'string'],
			"email" => ['nullable', 'email'],
			"phone" => ['nullable', 'string'],
			"whatsapp" => ['nullable', 'string'],
			"address" => ['nullable', 'string'],
			"maps" => ['nullable', 'string'],
			"policy_en" => ['required', 'string'],
			"policy_ar" => ['required', 'string'],
			"terms_en" => ['required', 'string'],
			"terms_ar" => ['required', 'string'],
			"desc_en" => ['required', 'string'],
			"desc_ar" => ['required', 'string'],
			"about_ar" => ['required', 'string'],
			"about_en" => ['required', 'string'],
			"logo" => ['nullable', 'string'],
			"facebook" => ['nullable', 'string'],
			"twitter" => ['nullable', 'string'],
			"youtube" => ['nullable', 'string'],
			"instagram" => ['nullable', 'string'],
			"g_play_app" => ['nullable', 'string'],
			"app_store_app" => ['nullable', 'string'],
			"carpets_hours" => ['required', 'numeric'],
			"clothes_hours" => ['required', 'numeric'],
			"furniture_hours" => ['required', 'numeric'],
			"max_carpet_area" => ['required', 'numeric', 'min:0'],
			"delivery_charge" => ['required', 'numeric'],
			"free_delivery" => ['required', 'numeric'],
			"points_per_spent_riyal" => ['required', 'numeric'],
			"riyal_per_point_redeem" => ['required', 'numeric'],
			"minium_points_to_use" => ['required', 'numeric'],
			"register_points" => ['required', 'numeric'],
			"referral_image_en" => ['nullable', 'string'],
			"referral_image_ar" => ['nullable', 'string'],
			"referral_points" => ['required', 'numeric'],
			"referral_riyals" => ['required', 'numeric'],
			"notify_login_using" => ['required', 'array'],
			"notify_client_using" => ['required', 'array'],
			"notify_representatives_using" => ['required', 'array'],
			"login_using" => ['required', 'string'],
			"not_available_message_en" => ['required', 'string'],
			"not_available_message_ar" => ['required', 'string'],
			"testing_accounts" => ['nullable', 'array'],
			"testing_accounts.*" => ['required', 'exists:users,id'],
			"welcome_notification_title" => ['nullable', 'string'],
			"welcome_notification_body" => ['nullable', 'string'],
			"abandoned_cart_notification_title" => ['nullable', 'string'],
			"abandoned_cart_notification_body" => ['nullable', 'string'],
			"inactive_new_users_notification_title" => ['nullable', 'string'],
			"inactive_new_users_notification_body" => ['nullable', 'string'],
			"inactive_after_order_notification_title" => ['nullable', 'string'],
			"inactive_after_order_notification_body" => ['nullable', 'string'],
			"feedback_after_completion_notification_title" => ['nullable', 'string'],
			"feedback_after_completion_notification_body" => ['nullable', 'string'],
			"celebrate_birthday_notification_title" => ['nullable', 'string'],
			"celebrate_birthday_notification_body" => ['nullable', 'string'],
			"week_prices" => ['nullable', 'array'],
			"week_prices.*.day" => ['required', 'string', 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday'],
			"week_prices.*.percentage" => ['required', 'numeric'],
			"no_order_notifications" => ['nullable', 'array'],
			"no_order_notifications.*.days" => ['required', 'numeric'],
			"no_order_notifications.*.notification_title" => ['required', 'string'],
			"no_order_notifications.*.notification_body" => ['required', 'string'],
			"no_order_notifications.*.is_active" => ['required', 'boolean'],
			"no_order_notifications.*.added_points" => ['required', 'numeric'],
			"no_order_notifications.*.money_expiry_days" => ['required', 'numeric'],
			"no_order_notifications.*.notes" => ['nullable', 'string'],
			"order_min_price" => ['required', 'numeric'],
			"first_order_min_price" => ['required', 'numeric'],
			"allowed_payment_methods" => ['required', 'array'],
			"allowed_payment_methods.*" => ['required', 'string', 'in:cash,card,points,wallet'],
			"multiple_payment_fees" => ['nullable', 'numeric', 'min:0'],
			"clean_station_commercial_registration" => ['nullable', 'string'],
			"clean_station_tax_number" => ['nullable', 'string'],
		];

	}

	/**
	 * Get the error messages for the defined validation rules.
	 *
	 * @return array
	 */
	public function messages()
	{
		return [
			"translations.en.text.string" => trans('name should be a string'),
			"translations.en.text.required" => trans('name is required'),
			"translations.ar.text.string" => trans('name should be a string'),
			"translations.ar.text.required" => trans('name is required'),
			"email.email" => trans('email should be a email'),
			"email.required" => trans('email is required'),
			"phone.string" => trans('phone should be a string'),
			"phone.required" => trans('phone is required'),
			"whatsapp.string" => trans('whatsapp should be a string'),
			"whatsapp.required" => trans('whatsapp is required'),
			"translations.en.policy.string" => trans('policy should be a string'),
			"translations.en.policy.required" => trans('policy is required'),
			"translations.ar.policy.string" => trans('policy should be a string'),
			"translations.ar.policy.required" => trans('policy is required'),
			"translations.en.terms.string" => trans('terms  should be a string'),
			"translations.en.terms.required" => trans('terms  is required'),
			"translations.ar.terms.string" => trans('terms  should be a string'),
			"translations.ar.terms.required" => trans('terms  is required'),
			"translations.en.description.string" => trans('description should be a string'),
			"translations.en.description.required" => trans('description is required'),
			"translations.ar.description.string" => trans('description should be a string'),
			"translations.ar.description.required" => trans('description is required'),
			"translations.en.about_us.string" => trans('about us should be a string'),
			"translations.en.about_us.required" => trans('about us is required'),
			"translations.ar.about_us.string" => trans('about us should be a string'),
			"translations.ar.about_us.required" => trans('about us is required'),
			"facebook.string" => trans('Facebook should be a string'),
			"facebook.required" => trans('Facebook is required'),
			"twitter.string" => trans('Twitter should be a string'),
			"twitter.required" => trans('Twitter is required'),
			"youtube.string" => trans('Youtube should be a string'),
			"youtube.required" => trans('Youtube is required'),
			"instagram.string" => trans('Instagram should be a string'),
			"instagram.required" => trans('Instagram is required'),
			"android_link.string" => trans('android link should be a string'),
			"android_link.required" => trans('android link is required'),
			"ios_link.string" => trans('ios link should be a string'),
			"ios_link.required" => trans('ios link is required'),
		];

	}

	protected function failedValidation(Validator $validator)
	{
		throw new HttpResponseException($this->returnValidationError($validator));
	}

}
