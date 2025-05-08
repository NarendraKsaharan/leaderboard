<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container text-center mt-5">
        <h1 class="mb-4">Leaderboard</h1>

        <a href="{{ route('leaderboard.recalculate') }}" class="btn btn-primary mb-4">Recalculate</a>
        <div class="position-absolute top-0 end-0 p-3">
            @if(Session::has('success'))
                <div id="alertBox" class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ Session::get('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @elseif(Session::has('error'))
                <div id="alertBox" class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ Session::get('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        </div>
    
        <form method="GET" action="{{ route('leaderboard.index') }}">
            <div class="row mb-4 justify-content-center">
                <div class="col-md-4">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                        placeholder="Search by User ID">
                </div>

                <div class="col-md-4">
                    <select name="filter" class="form-select">
                        <option value="">Select Period</option>
                        <option value="day" {{ request('filter') == 'day' ? 'selected' : '' }}>Today</option>
                        <option value="month" {{ request('filter') == 'month' ? 'selected' : '' }}>This Month</option>
                        <option value="year" {{ request('filter') == 'year' ? 'selected' : '' }}>This Year</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <button type="submit" class="btn btn-secondary w-100">Filter</button>
                </div>
            </div>
        </form>

        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Total Points</th>
                    <th>Rank</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->full_name }}</td>
                    <td>{{ $user->total_points }}</td>
                    <td>#{{ $user->rank }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4">No users found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const alertBox = document.getElementById('alertBox');
        if (alertBox) {
            setTimeout(() => {
                alertBox.classList.remove('show');
                alertBox.classList.add('fade');
            }, 3000);
        }
    </script>
</body>

</html>