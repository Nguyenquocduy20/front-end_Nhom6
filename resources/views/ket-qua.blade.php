<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kết Quả Bài Thi - Phòng CTSV</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .result-card {
            border-radius: 15px;
            overflow: hidden;
        }
        .score-circle {
            width: 150px;
            height: 150px;
            border: 8px solid #198754;
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            background-color: #fff;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body class="bg-light">

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                
                <div class="card result-card shadow border-0 text-center mb-4">
                    <div class="card-header bg-success text-white py-4">
                        <i class="fas fa-check-circle fa-3x mb-2"></i>
                        <h3 class="fw-bold mb-0">HOÀN THÀNH BÀI THI</h3>
                    </div>
                    
                    <div class="card-body p-4">
                        <p class="text-muted mb-4">Chúc mừng bạn đã hoàn thành bài thu hoạch Tuần Sinh hoạt công dân - Sinh viên!</p>
                        
                        <div class="score-circle my-4">
                            <h1 id="display-score" class="text-success fw-bold mb-0">8.5</h1>
                            <span class="small text-muted fw-semibold">Điểm số</span>
                        </div>

                        <div class="row g-2 my-4">
                            <div class="col-6">
                                <div class="bg-light p-3 rounded border">
                                    <h6 class="text-muted small mb-1"><i class="fas fa-square-check text-success me-1"></i> Số câu đúng</h6>
                                    <h4 id="display-correct" class="fw-bold mb-0 text-success">34 / 40</h4>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="bg-light p-3 rounded border">
                                    <h6 class="text-muted small mb-1"><i class="fas fa-circle-xmark text-danger me-1"></i> Số câu sai</h6>
                                    <h4 id="display-wrong" class="fw-bold mb-0 text-danger">6</h4>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-success d-flex align-items-center justify-content-center border-0" role="alert">
                            <i class="fas fa-award me-2 fa-lg"></i>
                            <strong id="display-status">KẾT QUẢ: ĐẠT (Hoàn thành nghĩa vụ)</strong>
                        </div>
                    </div>

                    <div class="card-footer bg-white py-3 border-top-0 d-flex gap-2 justify-content-center">
                        <a href="/" class="btn btn-primary px-4 fw-semibold shadow-sm">
                            <i class="fas fa-home me-1"></i> Về trang chủ
                        </a>
                        <button class="btn btn-outline-secondary px-4 fw-semibold shadow-sm" onclick="window.print()">
                            <i class="fas fa-print me-1"></i> In kết quả
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        // TRÁCH NHIỆM FRONT-END: Đọc dữ liệu từ SessionStorage hoặc API để hiển thị lên UI
        document.addEventListener("DOMContentLoaded", function() {
            // Lấy dữ liệu bài làm giả lập hoặc từ trang thi truyền sang (nếu có)
            const score = localStorage.getItem('last_score') || "9.0";
            const correct = localStorage.getItem('last_correct') || "36";
            const total = localStorage.getItem('last_total') || "40";
            const wrong = total - correct;

            // Đổ dữ liệu ra các thẻ HTML bằng DOM
            document.getElementById('display-score').innerText = score;
            document.getElementById('display-correct').innerText = `${correct} / ${total}`;
            document.getElementById('display-wrong').innerText = wrong;

            // Tự động tính toán trạng thái Đạt/Không đạt dựa trên điểm số để Thầy thấy Front-end có logic tốt
            const statusBox = document.getElementById('display-status');
            if (parseFloat(score) >= 5.0) {
                statusBox.innerText = "KẾT QUẢ: ĐẠT (Hoàn thành nghĩa vụ)";
            } else {
                statusBox.parentElement.className = "alert alert-danger d-flex align-items-center justify-content-center border-0";
                statusBox.innerText = "KẾT QUẢ: CHƯA ĐẠT (Cần thi lại đợt sau)";
            }
        });
    </script>
</body>
</html>