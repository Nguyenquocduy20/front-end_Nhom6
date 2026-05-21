<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hệ Thống Thi Trắc Nghiệm - Phòng CTSV</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .quiz-card {
            transition: all 0.3s ease;
            border-radius: 12px;
        }
        .quiz-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15) !important;
        }
        .badge-time {
            background-color: #e8f5e9;
            color: #2e7d32;
        }
    </style>
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#"><i class="fas fa-graduation-cap me-2"></i>CỔNG THI SINH VIÊN</a>
            <div class="text-white small">
                <i class="fas fa-user me-1"></i> Nguyễn Quốc Duy - DH52200573
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <div class="row mb-4">
            <div class="col-12 text-center text-md-start">
                <h3 class="fw-bold text-dark mb-1">DANH SÁCH BÀI THI TRẮC NGHIỆM</h3>
                <p class="text-muted">Vui lòng chọn đúng bài thu hoạch hoặc bài kiểm tra được chỉ định để tiến hành làm bài.</p>
            </div>
        </div>

        <div class="row g-4" id="quiz-list-container">
            <div class="text-center my-5">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="mt-2">Đang tải danh sách đề thi...</p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            fetchQuizList();
        });

        // TRÁCH NHIỆM FRONT-END: Giả lập dữ liệu bốc từ DB thực tế của nhóm bạn để vẽ UI
       // Thay thế hàm fetchQuizList() cũ bằng hàm này để sẵn sàng ăn khớp dữ liệu thật
        function fetchQuizList() {
            // 1. Front-end gọi lệnh fetch() gửi request lên Server Laravel
            fetch('/api/quizzes') 
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Lỗi kết nối API hoặc Back-end chưa mở endpoint này');
                    }
                    return response.json(); // Chuyển đổi dữ liệu thô nhận được thành JSON
                })
                .then(res => {
                    // 2. Nếu Back-end phản hồi trạng thái thành công và trả về mảng dữ liệu thật
                    if (res.status === "success" && res.data) {
                        // Duy bốc toàn bộ dữ liệu thật đổ vào hàm render để vẽ lên màn hình
                        renderQuizList(res.data);
                    }
                })
                .catch(err => {
                    console.error("Chưa kết nối được với Database thật, đang dùng dữ liệu dự phòng để tránh lỗi giao diện:", err);
                    
                    // Giữ lại mảng 4 bài thi cũ làm dữ liệu dự phòng (Backup) phòng khi Back-end bị sập 
                    // thì giao diện của Duy khi mang đi Demo báo cáo vẫn hiện đầy đủ không bị trắng trang.
                    const backupData = [
                        { quid: 19, quiz_name: "Bài kiểm tra thử (Demo)", description: "Hệ thống phòng thi trắc nghiệm gánh tải.", duration: 15, noq: 5 },
                        { quid: 28, quiz_name: "Bài thu hoạch Tuần SHCD - SV giữa khóa năm học 2020-2021", description: "Nội dung học tập nghị quyết, quy chế đào tạo.", duration: 40, noq: 35 },
                        { quid: 31, quiz_name: "Bài thu hoạch Tuần SHCD - SV đầu khóa năm học 2020-2021", description: "Dành cho tân sinh viên tìm hiểu về truyền thống nhà trường.", duration: 40, noq: 35 },
                        { quid: 52, quiz_name: "Bài thu hoạch Tuần SHCD - SV giữa khoá năm học 2021-2022", description: "Cập nhật tình hình kinh tế - xã hội, các quy định mới.", duration: 40, noq: 45 }
                    ];
                    renderQuizList(backupData);
                });
        }

        // Hàm sinh HTML động từ mảng dữ liệu
        function renderQuizList(quizzes) {
            const container = document.getElementById('quiz-list-container');
            container.innerHTML = ''; // Xóa spinner loading

            quizzes.forEach(quiz => {
                const cardHtml = `
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 shadow-sm border-0 quiz-card">
                            <div class="card-body p-4 d-flex flex-column">
                                <span class="badge badge-time mb-3 align-self-start py-2 px-3 fw-bold">
                                    <i class="far fa-clock me-1"></i> ${quiz.duration} Phút
                                </span>
                                <h5 class="card-title fw-bold text-dark text-line-2 mb-2">${quiz.quiz_name}</h5>
                                <p class="card-text text-muted small text-line-3 flex-grow-1">${quiz.description}</p>
                                
                                <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                                    <span class="small text-secondary fw-semibold">
                                        <i class="fas fa-list-ol me-1"></i> ${quiz.noq} Câu hỏi
                                    </span>
                                    <button class="btn btn-primary btn-sm fw-bold px-3 py-2 shadow-sm" onclick="startExam(${quiz.quid})">
                                        VÀO THI <i class="fas fa-arrow-right ms-1"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                container.insertAdjacentHTML('beforeend', cardHtml);
            });
        }

        // Hàm xử lý điều hướng khi SV chọn một đề cụ thể
        function startExam(quid) {
            // Lưu mã đề thi người dùng chọn vào localStorage
            localStorage.setItem('selected_quiz_id', quid);
            
            // Điều hướng sang phòng thi
            window.location.href = '/phong-thi';
        }
    </script>
</body>
</html>