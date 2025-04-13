package com.example.moffatbay.servlets;

import java.io.IOException;
import java.io.PrintWriter;
import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import javax.servlet.ServletException;
import javax.servlet.annotation.WebServlet;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import com.google.gson.JsonObject;
import com.google.gson.JsonParser;

@WebServlet("/LoginServlet")
public class LoginServlet extends HttpServlet {

    private static final String DB_URL = "jdbc:mysql://localhost:3306/moffat_bay_marina?useSSL=false&serverTimezone=UTC";
    private static final String DB_USER = "root";
    private static final String DB_PASSWORD = "";

    @Override
    protected void doPost(HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException {

        response.setContentType("application/json");
        PrintWriter out = response.getWriter();

        try {
            // 1. Read JSON data from request
            StringBuilder sb = new StringBuilder();
            String s;
            while ((s = request.getReader().readLine()) != null) {
                sb.append(s);
            }
            JsonObject jsonInput = JsonParser.parseString(sb.toString()).getAsJsonObject();

            String email = jsonInput.get("email").getAsString();
            String password = jsonInput.get("password").getAsString();

            // 2. Database logic
            try {
                Class.forName("com.mysql.cj.jdbc.Driver");
                Connection con = DriverManager.getConnection(DB_URL, DB_USER, DB_PASSWORD);

                // Check if the user exists and the password matches
                String sql = "SELECT * FROM Customers WHERE email = ? AND passwordHash = ?"; // In real application Hash password
                PreparedStatement pst = con.prepareStatement(sql);
                pst.setString(1, email);
                pst.setString(2, password); // In real application, Hash password before checking
                ResultSet rs = pst.executeQuery();

                if (rs.next()) {
                    // Login successful
                    JsonObject jsonOutput = new JsonObject();
                    jsonOutput.addProperty("message", "Login successful!");
                    out.print(jsonOutput.toString());
                    out.flush();

                   con.close();
                } else {
                    // Invalid credentials
                    response.setStatus(401); // Unauthorized
                    JsonObject jsonOutput = new JsonObject();
                    jsonOutput.addProperty("message", "Invalid credentials.");
                    out.print(jsonOutput.toString());
                    out.flush();
                    con.close();
                }

            } catch (ClassNotFoundException | SQLException e) {
                System.err.println(e.getMessage());
                response.setStatus(500); // Internal Server Error
                JsonObject jsonOutput = new JsonObject();
                jsonOutput.addProperty("message", "Login failed!");
                out.print(jsonOutput.toString());
                out.flush();
            }

        } catch (Exception e) {
            response.setStatus(400); // Bad Request
            JsonObject jsonOutput = new JsonObject();
            jsonOutput.addProperty("message", "Invalid input data!");
            out.print(jsonOutput.toString());
            out.flush();
        }
    }
}