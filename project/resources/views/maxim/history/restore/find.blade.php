<div class="col-sm-8 col-sm-offset-2">
    <div class="col-sm-3">
        <span>
            <h3>Find Details</h3>
        </span>
    </div>
    <form action="{{Route('sent_find_request')}}" method="post">
        {{csrf_field()}}
        <input type="hidden" name="filter_type" value="{{Session::get('type')}}">
        <div class="col-sm-7">
            <div class="form-group" style="margin-top: 15px;">
                <input type="text" name="filter_value" class="form-control" placeholder="Find" value="{{old('filter_value')}}">
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group" style="margin-top: 15px;">
                <button class="form-control" type="submit"> Search</button>
            </div>
        </div>
    </form>
</div>