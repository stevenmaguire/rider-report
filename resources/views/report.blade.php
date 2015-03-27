<html>
    <head>
        <title>Laravel</title>
        <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/foundation/5.5.1/css/foundation.min.css" />
    </head>
    <body>
        <section>
            <div class="row">
                <div class="small-12 medium-4 columns">
                    <div class="panel">{{ $report->getTripCount() }} trips</div>
                </div>
                <div class="small-12 medium-4 columns">
                    <div class="panel">{{ $report->getTotalDistance() }} miles</div>
                </div>
                <div class="small-12 medium-4 columns">
                    <div class="panel">{{ $report->getTotalTime() }}</div>
                </div>
            </div>
        </section>
        <section>
            <div class="row">
                <div class="small-12 medium-6 columns">
                    <div class="panel">average {{ $report->getAverageDistance() }} miles per trip</div>
                </div>
                <div class="small-12 medium-6 columns">
                    <div class="panel">average {{ $report->getAverageTime() }} per trip</div>
                </div>
            </div>
        </section>
        <section>
            <div class="row">
                <div class="small-12 medium-4 columns">
                    <div class="panel">first trip on {{ $report->getFirstTrip() }}</div>
                </div>
                <div class="small-12 medium-4 columns">
                    <div class="panel">across {{ $report->getTimeSpan() }}</div>
                </div>
                <div class="small-12 medium-4 columns">
                    <div class="panel">last trip on {{ $report->getLastTrip() }}</div>
                </div>
            </div>
        </section>
    </body>
</html>
