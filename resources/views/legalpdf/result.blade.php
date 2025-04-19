@extends('layouts.app')

@section('content')
    <div class="card shadow">
        <div class="card-header bg-success text-white">
            <h4>Thread Found</h4>
        </div>
        <div class="card-body">
            <p><strong>Thread ID:</strong> <code>{{ $threadId }}</code></p>
            <a href="{{ route('legalpdf.generate', $threadId) }}" class="btn btn-primary">
                📄 Generate PDF
            </a>
            <div class="progress mt-3 d-none" id="progressWrap">
                <div id="pdfProgress" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
                     style="width: 0%"></div>
            </div>
        </div>
    </div>
    <script>
        document.getElementById('generatePdfBtn').addEventListener('click', function (e) {
            e.preventDefault();
    
            const progressBar = document.getElementById('pdfProgress');
            const progressWrap = document.getElementById('progressWrap');
    
            progressWrap.classList.remove('d-none');
            progressBar.style.width = '5%';
            progressBar.innerText = 'Starting...';
    
            let interval = setInterval(() => {
                let width = parseInt(progressBar.style.width);
                if (width < 95) {
                    width += 5;
                    progressBar.style.width = width + '%';
                    progressBar.innerText = `Generating... ${width}%`;
                }
            }, 1000);
    
            fetch("{{ route('legalpdf.generate', $threadId) }}")
                .then(response => {
                    clearInterval(interval);
                    progressBar.style.width = '100%';
                    progressBar.classList.remove('progress-bar-animated');
                    progressBar.innerText = 'Done ✅';
    
                    response.blob().then(blob => {
                        const url = window.URL.createObjectURL(blob);
                        const link = document.createElement('a');
                        link.href = url;
                        link.download = 'legal_thread.pdf';
                        link.click();
                    });
                })
                .catch(err => {
                    clearInterval(interval);
                    progressBar.classList.add('bg-danger');
                    progressBar.innerText = 'Error generating PDF';
                });
        });
    </script>
    
@endsection
