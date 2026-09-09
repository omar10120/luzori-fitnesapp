@push('scripts')
  <style>
      .client-search {
          min-width: 280px;
          margin-right: 0.75rem;
      }

      .client-search .client-search-input {
          min-width: 280px;
          height: 38px;
          border-radius: 12px;
          border: 1px solid rgba(255,255,255,0.12);
          background: rgba(255,255,255,0.04);
          color: #fff;
          padding: 0.55rem 0.85rem;
      }

      .client-search .client-search-input::placeholder {
          color: rgba(255,255,255,0.6);
      }

      .client-search-results {
          position: absolute;
          right: 0;
          top: calc(100% + 8px);
          width: min(360px, 90vw);
          background: rgba(14, 18, 27, 0.98);
          border: 1px solid rgba(255,255,255,0.08);
          border-radius: 12px;
          box-shadow: 0 16px 36px rgba(0, 0, 0, 0.35);
          z-index: 1200;
          display: none;
          overflow: hidden;
      }

      .client-search-results.show {
          display: block;
      }

      .client-search-result-item {
          display: flex;
          align-items: center;
          gap: 0.75rem;
          padding: 0.7rem 0.85rem;
          color: #f5f7fb;
          text-decoration: none;
          border-bottom: 1px solid rgba(255,255,255,0.06);
      }

      .client-search-result-item:last-child {
          border-bottom: none;
      }

      .client-search-result-item:hover {
          background: rgba(255,255,255,0.04);
          color: #fff;
      }

      .client-search-avatar {
          width: 34px;
          height: 34px;
          border-radius: 50%;
          background: linear-gradient(135deg, #f16a1b, #ff9a5a);
          display: flex;
          align-items: center;
          justify-content: center;
          font-weight: 700;
          color: #fff;
          flex-shrink: 0;
      }

      .client-search-meta {
          min-width: 0;
      }

      .client-search-name {
          font-size: 0.9rem;
          font-weight: 600;
          line-height: 1.2;
      }

      .client-search-email {
          font-size: 0.75rem;
          color: rgba(255,255,255,0.7);
          line-height: 1.2;
          white-space: nowrap;
          overflow: hidden;
          text-overflow: ellipsis;
      }

      .client-search-empty {
          padding: 0.85rem 0.9rem;
          color: rgba(255,255,255,0.7);
          font-size: 0.85rem;
      }

      .notification-dropdown {
          margin-right: 0.5rem;
      }

      .notification-link {
          position: relative;
          display: flex;
          align-items: center;
          justify-content: center;
          width: 40px;
          height: 40px;
          border-radius: 12px;
          color: rgba(255,255,255,0.9);
      }

      .notification-link:hover {
          color: #fff;
          background: rgba(255,255,255,0.04);
      }

      .notification-badge {
          position: absolute;
          top: 5px;
          right: 5px;
          min-width: 18px;
          height: 18px;
          padding: 0 0.3rem;
          border-radius: 999px;
          background: #f16a1b;
          color: #fff;
          font-size: 0.65rem;
          font-weight: 700;
          line-height: 18px;
          text-align: center;
      }

      .notification-menu {
          width: min(360px, 90vw);
          border-radius: 16px;
          border: 1px solid rgba(255,255,255,0.08);
          background: rgba(14, 18, 27, 0.98);
          box-shadow: 0 22px 40px rgba(0, 0, 0, 0.32);
          overflow: hidden;
          padding: 0;
      }

      .notification-header {
          display: flex;
          align-items: center;
          justify-content: space-between;
          padding: 0.75rem 0.9rem;
          border-bottom: 1px solid rgba(255,255,255,0.06);
          color: #fff;
          font-weight: 600;
      }

      .notification-list {
          max-height: 320px;
          overflow-y: auto;
      }

      .notification-item {
          display: block;
          padding: 0.8rem 0.9rem;
          border-bottom: 1px solid rgba(255,255,255,0.06);
          color: rgba(255,255,255,0.88);
          text-decoration: none;
      }

      .notification-item:last-child {
          border-bottom: none;
      }

      .notification-item:hover {
          background: rgba(255,255,255,0.04);
          color: #fff;
      }

      .notification-item.unread {
          background: rgba(241, 106, 27, 0.08);
      }

      .notification-topline {
          display: flex;
          align-items: center;
          justify-content: space-between;
          gap: 0.5rem;
          margin-bottom: 0.2rem;
      }

      .notification-title {
          font-size: 0.82rem;
          font-weight: 600;
      }

      .notification-time {
          font-size: 0.68rem;
          color: rgba(255,255,255,0.65);
      }

      .notification-message {
          font-size: 0.75rem;
          color: rgba(255,255,255,0.72);
          line-height: 1.4;
      }

      .notification-empty {
          padding: 1rem 0.9rem;
          color: rgba(255,255,255,0.7);
          font-size: 0.8rem;
          text-align: center;
      }

      @media (max-width: 991.98px) {
          .client-search {
              width: 100%;
              min-width: 0;
              margin: 0.5rem 0 0;
          }

          .client-search .client-search-input {
              width: 100%;
              min-width: 0;
          }
      }
  </style>
  <script>
      $(document).ready(function() {
          const savedTheme = localStorage.getItem('theme');
          if (savedTheme) {
              if (savedTheme === 'dark') {
                  $('body').addClass('dark');
                  $('.sit_darkcolor_theam').hide();
                  $('.sit_lightcolor_theam').show();
              } else if(savedTheme === 'light'){
                  $('body').removeClass('dark');
                  $('.sit_darkcolor_theam').show();
                  $('.sit_lightcolor_theam').hide();
              }
          }
          $(".sit_color_theam").click(function() {
              let selectedMode = $(this).data("value");
              if (selectedMode === "dark") {
                  $('body').addClass('dark');
                  $('.sit_darkcolor_theam').hide();
                  $('.sit_lightcolor_theam').show();
                  localStorage.setItem('theme', 'dark');
              } else if(selectedMode === 'light') {
                  $('body').removeClass('dark');
                  $('.sit_darkcolor_theam').show();
                  $('.sit_lightcolor_theam').hide();
                  localStorage.setItem('theme', 'light');
              }
          });

          const $clientSearchInput = $('#client-search-input');
          const $clientSearchResults = $('#client-search-results');
          let clientSearchTimer = null;

          function renderClientResults(results) {
              if (!results.length) {
                  $clientSearchResults.html('<div class="client-search-empty">No client found</div>').addClass('show');
                  return;
              }

              const html = results.map(function(item) {
                  const initials = (item.name || 'U').charAt(0).toUpperCase();

                  return `
                      <a href="${item.profile_url}" class="client-search-result-item">
                          <div class="client-search-avatar">${initials}</div>
                          <div class="client-search-meta">
                              <div class="client-search-name">${item.name}</div>
                              <div class="client-search-email">${item.email}</div>
                          </div>
                      </a>
                  `;
              }).join('');

              $clientSearchResults.html(html).addClass('show');
          }

          function searchClients() {
              const query = $.trim($clientSearchInput.val());

              if (!query) {
                  $clientSearchResults.removeClass('show').empty();
                  return;
              }

              if (clientSearchTimer) {
                  clearTimeout(clientSearchTimer);
              }

              clientSearchTimer = setTimeout(function() {
                  $.ajax({
                      url: "{{ route('user.search') }}",
                      type: 'GET',
                      data: { q: query },
                      dataType: 'json',
                      success: function(response) {
                          if (response && response.status) {
                              renderClientResults(response.results || []);
                          }
                      }
                  });
              }, 200);
          }

          $clientSearchInput.on('input', searchClients);

          $clientSearchInput.on('keydown', function(event) {
              if (event.key !== 'Enter') {
                  return;
              }

              event.preventDefault();

              const firstResult = $clientSearchResults.find('.client-search-result-item').first();
              if (firstResult.length) {
                  window.location.href = firstResult.attr('href');
                  return;
              }

              $clientSearchResults.removeClass('show').empty();
          });

          $(document).on('click', function(event) {
              if (!$(event.target).closest('.client-search').length) {
                  $clientSearchResults.removeClass('show');
              }
          });
      });
  </script>
@endpush
<nav class="nav navbar navbar-expand-lg navbar-light iq-navbar">
  <div class="container-fluid navbar-inner">
       <a href="{{route('dashboard')}}" class="navbar-brand">
            <div class="logo-main">
                <div class="logo-normal">
					          <img class="site_logo_preview" src="{{ getSingleMedia(appSettingData('get'), 'site_logo',null) }}" height="30" alt="site_logo">
                </div>
                <div class="logo-normal dark-normal">
					          <img class="site_dark_logo_preview" src="{{ getSingleMedia(appSettingData('get'), 'site_dark_logo',null) }}" height="30" alt="site_dark_logo">
                </div>
            </div>
      </a>
    <div class="sidebar-toggle" data-toggle="sidebar" data-active="true">
      <i class="icon">
        <svg width="20px" height="20px" viewBox="0 0 24 24">
          <path fill="currentColor" d="M4,11V13H16L10.5,18.5L11.92,19.92L19.84,12L11.92,4.08L10.5,5.5L16,11H4Z" />
      </svg>
      </i>
    </div>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <!-- <span class="navbar-toggler-icon"></span> -->
      <span class="navbar-toggler-icon">
        <span class="navbar-toggler-bar bar1 mt-2"></span>
        <span class="navbar-toggler-bar bar2"></span>
        <span class="navbar-toggler-bar bar3"></span>
      </span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      @php
          $authUser = auth()->user();
          $headerNotifications = $authUser ? $authUser->notifications()->latest()->limit(5)->get() : collect();
          $headerUnreadNotifications = $authUser ? $authUser->unreadNotifications()->count() : 0;
      @endphp
      <ul class="navbar-nav ms-auto  navbar-list mb-2 mb-lg-0 mt-3">
        <li class="nav-item client-search">
          <div class="position-relative">
              <input
                  id="client-search-input"
                  type="text"
                  class="form-control client-search-input"
                  placeholder="Search client"
                  autocomplete="off"
              >
              <div id="client-search-results" class="client-search-results"></div>
          </div>
        </li>
        <li class="nav-item">
          <a href="{{ url('/admin/exercise/create') }}" class="btn btn-primary btn-sm rounded-pill px-3 mt-1">
            Create New Exercise
          </a>
        </li>
        <li class="nav-item dropdown notification-dropdown">
          <a class="nav-link notification-link" href="#" id="navbarNotificationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        
               <img src="{{ asset('vendor/Iconly/Bold/Notification.svg') }}" 
                alt="{{ __('message.notifications') }}" 
                width="24" 
                height="24">

              @if($headerUnreadNotifications > 0)
                <span class="notification-badge">{{ $headerUnreadNotifications }}</span>
              @endif
          </a>
          <div class="dropdown-menu dropdown-menu-end notification-menu" aria-labelledby="navbarNotificationDropdown">
            <div class="notification-header">
              <span>Notifications</span>
              @if($headerUnreadNotifications > 0)
                <span class="badge bg-primary rounded-pill">{{ $headerUnreadNotifications }}</span>
              @endif
            </div>
            <div class="notification-list">
              @forelse($headerNotifications as $notification)
                @php
                    $notificationData = $notification->data ?? [];
                    $notificationTitle = $notificationData['subject'] ?? $notificationData['title'] ?? __('message.notification');
                    $notificationMessage = $notificationData['message'] ?? __('message.notification');
                @endphp
                <a href="javascript:void(0)" class="notification-item {{ $notification->read_at ? '' : 'unread' }}">
                  <div class="notification-topline">
                    <span class="notification-title">{{ Str::limit($notificationTitle, 40) }}</span>
                    <span class="notification-time">{{ timeAgoFormate($notification->created_at) }}</span>
                  </div>
                  <div class="notification-message">{{ Str::limit(strip_tags($notificationMessage), 90) }}</div>
                </a>
              @empty
                <div class="notification-empty">No notifications</div>
              @endforelse
            </div>
          </div>
        </li>
        <li class="nav-item theme-scheme-dropdown dropdown iq-dropdown">
            <div class="btn sit_color_theam sit_darkcolor_theam" data-bs-toggle="tooltip" title="{{ __('message.sit_dark_color_theam') }}" data-setting="color-mode" data-name="color" data-value="dark">
              <svg width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill="currentColor" d="M12,8A4,4 0 0,0 8,12A4,4 0 0,0 12,16A4,4 0 0,0 16,12A4,4 0 0,0 12,8M12,18A6,6 0 0,1 6,12A6,6 0 0,1 12,6A6,6 0 0,1 18,12A6,6 0 0,1 12,18M20,8.69V4H15.31L12,0.69L8.69,4H4V8.69L0.69,12L4,15.31V20H8.69L12,23.31L15.31,20H20V15.31L23.31,12L20,8.69Z" />
              </svg>
            </div>
            <div class="btn active sit_color_theam sit_lightcolor_theam" data-bs-toggle="tooltip" title="{{ __('message.sit_light_color_theam') }}" data-setting="color-mode" data-name="color" data-value="light" style="display:none;">
              <svg width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path fill="currentColor" d="M9,2C7.95,2 6.95,2.16 6,2.46C10.06,3.73 13,7.5 13,12C13,16.5 10.06,20.27 6,21.54C6.95,21.84 7.95,22 9,22A10,10 0 0,0 19,12A10,10 0 0,0 9,2Z" />
              </svg>
            </div>   
        </li>
        <li class="nav-item dropdown">
          <a href="#" class="search-toggle nav-link" id="dropdownMenuButton2" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            @php
                $selected_lang_flag = file_exists(public_path('/images/flag/' .app()->getLocale() . '.svg')) ? asset('/images/flag/' . app()->getLocale() . '.svg') : asset('/images/lang_flag.svg');
            @endphp
            <img src="{{ $selected_lang_flag }}" class="img-fluid rounded selected-lang" alt="lang-flag">
            <span class="bg-primary"></span>
          </a>
          <div class="sub-drop dropdown-menu dropdown-menu-end p-0 language-menu" aria-labelledby="dropdownMenuButton2">
            <div class="card shadow-none m-0 border-0">
              <div class=" p-0 ">
                <ul class="list-group list-group-flush">
                @php
                    $language_option = appSettingData('get')->language_option;
                        if(!empty($language_option)){
                            $language_array = languagesArray($language_option);
                        }
                    @endphp
                    @if(count($language_array) > 0 )
                        @foreach( $language_array  as $lang )
                            <li class="iq-sub-card list-group-item">
                                <a class="dropdown-item p-0" data-lang="{{ $lang['id'] }}" href="{{ route('change.language',[ 'locale' => $lang['id'] ]) }}">
                                @php
                                    $flag_path = file_exists(public_path('/images/flag/' . $lang['id'] . '.svg')) ? asset('/images/flag/' . $lang['id'] . '.svg') : asset('/images/lang_flag.svg');
                                @endphp
                                    <img src="{{ $flag_path }}" alt="img-flag-{{ $lang['id'] }}" class="img-fluid me-2 selected-lang-list" />
                                    {{ $lang['title'] }}
                                </a>
                            </li>
                        @endforeach
                    @endif
                </ul>
              </div>
            </div>
          </div>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link py-0 d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
          <img src="{{ getSingleMedia(auth()->user(),'profile_image', null) }}" alt="User-Profile" class="img-fluid avatar avatar-50 avatar-rounded">
            <div class="caption ms-3 d-none d-md-block ">
              <h6 class="mb-0 caption-title">{{ auth()->user()->display_name }}</h6>
            </div>
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
            <li><a class="dropdown-item" href="{{ route('setting.index',[ 'page' => 'profile-form' ]) }}">{{ __('message.profile') }}</a></li>
            <li><a class="dropdown-item" href="{{ route('setting.index',[ 'page' => 'password-form' ]) }}">{{ __('message.change_password') }}</a></li>
            <li><a class="dropdown-item" href="{{ route('setting.index') }}">{{ __('message.setting') }}</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><form method="POST" action="{{route('logout')}}">
              @csrf
              <a href="javascript:void(0)" class="dropdown-item"
                onclick="event.preventDefault();
              this.closest('form').submit();">
                  {{ __('message.logout') }}
              </a>
              </form>
            </li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>


