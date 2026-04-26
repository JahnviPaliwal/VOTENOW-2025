// ============================================================
// script.js - Main JavaScript File
// Handles OTP generation, chart rendering, animations
// ============================================================

// ----- Landing Page Auto-Redirect -----
// Called from index.php after 2.5 seconds
function startRedirect(url, seconds) {
    let remaining = seconds;
    const countEl = document.getElementById('countdown');
    const interval = setInterval(function () {
        remaining--;
        if (countEl) countEl.textContent = remaining;
        if (remaining <= 0) {
            clearInterval(interval);
            window.location.href = url;
        }
    }, 1000);
}

// ----- OTP Simulation -----
// Generates a random 6-digit OTP and stores in hidden field + displays it
function generateOTP() {
    const otp = Math.floor(100000 + Math.random() * 900000); // 6-digit OTP
    document.getElementById('otp_generated').value = otp;
    document.getElementById('otp_display_code').textContent = otp;
    document.getElementById('otp-box').style.display = 'block';
    document.getElementById('verify-section').style.display = 'block';
    document.getElementById('generate-btn').textContent = 'Resend OTP';
    document.getElementById('generate-btn').style.backgroundColor = '#f9ab00';
    return false; // prevent form submit
}

// ----- Phone Number Validation -----
function validatePhone(input) {
    input.value = input.value.replace(/[^0-9]/g, '');
    if (input.value.length > 10) {
        input.value = input.value.substring(0, 10);
    }
}

// ----- Bar Chart for Results -----
// Draws a simple bar chart using Chart.js (loaded via CDN in results.php)
function drawResultsChart(labels, data, totalVotes) {
    const ctx = document.getElementById('resultsChart').getContext('2d');

    // Generate colors for each bar
    const colors = [
        'rgba(26, 115, 232, 0.85)',
        'rgba(52, 168, 83, 0.85)',
        'rgba(251, 188, 4, 0.85)',
        'rgba(234, 67, 53, 0.85)',
        'rgba(103, 58, 183, 0.85)'
    ];

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Votes Received',
                data: data,
                backgroundColor: colors.slice(0, data.length),
                borderColor: colors.slice(0, data.length).map(c => c.replace('0.85', '1')),
                borderWidth: 1.5,
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            const votes = context.parsed.y;
                            const pct = totalVotes > 0 ? ((votes / totalVotes) * 100).toFixed(1) : 0;
                            return ' ' + votes + ' votes (' + pct + '%)';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        precision: 0
                    },
                    title: {
                        display: true,
                        text: 'Number of Votes'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Candidates'
                    }
                }
            }
        }
    });
}

// ----- Confirm Vote -----
function confirmVote(candidateName) {
    return confirm('Are you sure you want to vote for ' + candidateName + '?\nThis action cannot be undone.');
}

// ----- Animate vote bars on results page -----
document.addEventListener('DOMContentLoaded', function () {
    const bars = document.querySelectorAll('.vote-bar');
    bars.forEach(function (bar) {
        const targetWidth = bar.getAttribute('data-width');
        setTimeout(function () {
            bar.style.width = targetWidth + '%';
        }, 100);
    });
});
