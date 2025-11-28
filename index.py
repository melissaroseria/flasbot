from flask import Flask, render_template, request, jsonify
from services.get import send  # File Market ve Kredim için birleşik gateway

app = Flask(__name__, template_folder="templates", static_folder="static")

@app.route("/")
def panel():
    return render_template("panel.html")

# Tek parametre: mobile. İki provider çağrılır ve birleşik yanıt döner.
@app.route("/send", methods=["POST"])
def send_route():
    mobile = request.form.get("mobile") or (request.json and request.json.get("mobile"))
    if not mobile:
        return jsonify({"success": False, "message": "mobile parametresi gerekli"}), 400

    result_file = send(mobile, provider="file")
    result_kredim = send(mobile, provider="kredim")

    return jsonify({"file_market": result_file, "kredim": result_kredim})

if __name__ == "__main__":
    app.run(debug=True)
