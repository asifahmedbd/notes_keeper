<li class="d-none d-sm-block">
    <form class="app-search" action="" method="post">
        @csrf
        <div class="app-search-box">
            <div class="input-group">
                <input type="text" class="form-control" style="width: 90px" name="order_id" placeholder="Search Files" required autocomplete="off">
                <div class="input-group-append">
                    <button class="btn" type="submit">
                        <i class="fe-search"></i>
                    </button>
                </div>
            </div>
        </div>
    </form>
</li>
