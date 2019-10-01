<form class="kt-form kt-form--label-right form_detail" action="{{ route($route.'.update_menus', ['id' => $id] )}}" method="post">
	{{csrf_field()}}
    <div class="form-group">
        <label for="recipient-name" class="form-control-label">Menu link:</label>
        <input type="text" class="form-control" name="menu_link" autocomplete="off" tabindex="1" value="{{$records[0]->menu_link}}">
    </div>
    <div class="form-group">
        <label for="message-text" class="form-control-label">Menu icon:</label>
        <input type="text" class="form-control" name="menu_icon" autocomplete="off" tabindex="2" value="{{$records[0]->menu_icon}}">
        <span class="form-text text-muted">Only acceptable flaticon</span>
    </div>
</form>