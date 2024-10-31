<main>
    <div class="row no-gutters">
        <div class="col-md-3"></div>
        <div class="col-md-6 d-flex flex-column mt-5" style="border: 1px solid black">
            <div class="logo text-center">
                <img
                    src="{{URL::to('themes/default/assets/img/logo-wordcorp_600.png')}}"
                    alt="Logo"
                    class="img-fluid"
                />
            </div>
            
            <div
                class="flex-grow-1 d-flex align-items-center justify-content-center p-5">
                
                <form id="forgot-password-form" class="w-100" method="POST" action='{{URL::to("password-reset")}}'>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                    <div class="form-row">
                        <div class="form-group col-md-6 offset-md-3">
                            <h4>Password reset</h4>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6 offset-md-3">
                            <label for="password">New Password</label>
                            <input type="hidden" name="email" class="form-control" id="email" value="{{ $email }}"/>
                            <input type="password" name="password" class="form-control" id="password" />
                        </div>
                    </div>
                    <div class="form-row">
                        
                        <div class="form-group col-md-6 offset-md-3">
                            <label for="confirm-password">Confirm Password</label>
                            <input name="confirm_password"
                                type="password"
                                class="form-control"
                                id="confirm-password"
                            />
                            
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6 offset-md-3 text-right">
                            <button type="submit" class="btn btn-primary">Change</button>
                        </div>
                    </div>
                    
                </form>
            </div>
        </div>
    </div>
</main>
<script type="text/javascript" src="{{ asset('themes/default/assets/js/forgot-password.js') }}" ></script>