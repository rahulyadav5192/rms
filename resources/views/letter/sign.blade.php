<!-- resources/views/signature.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Signature on Image</title>
    <style>
        #signature-container {
            border: 1px solid #000;
            width: 600px;
            height: 300px;
        }

        canvas {
            width: 100%;
            height: 100%;
            touch-action: none;
        }
    </style>
</head>
<body>
    <h2>Sign Below</h2>
    <div id="signature-container">
        <canvas id="signature-pad"></canvas>
    </div>
    <br>
    <button onclick="clearPad()">Clear</button>
    <button onclick="submitSignature()">Place on Image</button>

    <form id="signature-form" method="POST" action="{{ route('signature.upload') }}">
        @csrf
        <input type="hidden" name="signature" id="signature-input">
    </form>

    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
    <script>
        const canvas = document.getElementById('signature-pad');
        const container = document.getElementById('signature-container');
        const signaturePad = new SignaturePad(canvas, {
            minWidth: 1,
            maxWidth: 2.5,
            penColor: "black"
        });

        function resizeCanvas() {
            // Adjust for device pixel ratio
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = container.offsetWidth * ratio;
            canvas.height = container.offsetHeight * ratio;
            canvas.getContext("2d").scale(ratio, ratio);

            signaturePad.clear(); // Clears and resets drawing offsets
        }

        window.addEventListener("resize", resizeCanvas);
        resizeCanvas();

        function clearPad() {
            signaturePad.clear();
        }

        function submitSignature() {
            if (!signaturePad.isEmpty()) {
                const dataUrl = signaturePad.toDataURL();
                document.getElementById('signature-input').value = dataUrl;
                document.getElementById('signature-form').submit();
            } else {
                alert("Please sign first.");
            }
        }
    </script>
</body>
</html>
