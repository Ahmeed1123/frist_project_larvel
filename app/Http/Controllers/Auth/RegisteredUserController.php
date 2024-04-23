<?php

namespace App\Http\Controllers\Auth;

use App\Enums\Types\OpportunityType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\StoreRegisteredUserRequest;
use App\Models\User;
use App\Notifications\NotificationReceived;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(StoreRegisteredUserRequest $request): \Illuminate\Http\RedirectResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole($request->role);

        if ($user->isOpportunityProvider()) {
            $notification = Notification::make()
                ->title(__('مرحباً بك في نظام فرصة!'))
                ->body(__('قم بإنشاء فرصة جديدة الآن.'))
                ->warning()
                ->actions([
                    Action::make('button')
                        ->label(__('إنشاء فرصة'))
                        ->button()
                        ->arguments([
                            'mail_action' => true,
                        ])
                        ->url(route('opportunities.create')),
                ])
                ->viewData([
                    'mail_lines' => [
                        __('في نظام فرصة يمكنك الوصول إلى أفضل الكفاءات والمواهب.'),
                        __('قم بإنشاء فرصة جديدة واستقبل الطلبات من الباحثين عن الفرص.'),
                        __('في حال وجود أي استفسار لا تتردد في التواصل معنا.'),
                        __('نتمنى لك تجربة ممتعة.'),
                    ]
                ])
                ->sendToDatabase($user);

            $user->notify(new NotificationReceived($notification));
        }elseif ($user->isOpportunist()){
            $notification = Notification::make()
                ->title(__('مرحباً بك في نظام فرصة!'))
                ->body(__('قم بالبحث عن وظيفة الآن.'))
                ->warning()
                ->actions([
                    Action::make('button')
                        ->label(__('البحث عن وظيفة'))
                        ->button()
                        ->arguments([
                            'mail_action' => true,
                        ])
                        ->url(route('opportunities.index.type', OpportunityType::JOB_ID)),
                ])
                ->viewData([
                    'mail_lines' => [
                        __('في نظام فرصة يمكنك البحث عن الفرص التي تناسبك والتقديم عليها بكل سهولة.'),
                        __('نسعى لتوفير الفرص التي تناسب احتياجاتك وتطلعاتك المستقبلية.'),
                        __('في حال وجود أي استفسار لا تتردد في التواصل معنا.'),
                        __('نتمنى لك تجربة ممتعة.'),
                    ]
                ])
                ->sendToDatabase($user);

            $user->notify(new NotificationReceived($notification));
        }


        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('user.profile.create');
    }
}
