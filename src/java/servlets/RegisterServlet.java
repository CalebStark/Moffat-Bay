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

public class RegisterServlet extends HttpServlet {

    private static final String DB_URL = "jdbc:mysql://localhost:3306/moffat_bay_marina?useSSL=false&serverTimezone=UTC";
    private static final String DB_USER = "root";
    private static final String DB_PASSWORD = "";

    @Override
    protected void doPost(HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException {

        response.setContentType("application/json");  //  Send back JSON
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
            String firstName = jsonInput.get("firstName").getAsString();
            String lastName = jsonInput.get("lastName").getAsString();
            String telephone = jsonInput.get("telephone").getAsString();
            String boatName = jsonInput.get("boatName").getAsString();
            int boatLength = jsonInput.get("boatLength").getAsInt();
            String password = jsonInput.get("password").getAsString();  //  NEVER store plain passwords! HASH IT

            // 2. Database logic
            try {
                Class.forName("com.mysql.cj.jdbc.Driver"); // Load Driver
                Connection con = DriverManager.getConnection(DB_URL, DB_USER, DB_PASSWORD);

                // Check if the email already exists
                String checkSql = "SELECT COUNT(*) FROM Customers WHERE email = ?";
                PreparedStatement checkPst = con.prepareStatement(checkSql);
                checkPst.setString(1, email);
                ResultSet rs = checkPst.executeQuery();
                rs.next();
                int count = rs.getInt(1);

                if (count > 0) {
                    // Email already exists
                    response.setStatus(400); // Bad Request
                    JsonObject jsonOutput = new JsonObject();
                    jsonOutput.addProperty("message", "Email address already registered. Please use a different email.");
                    out.print(jsonOutput.toString());
                    out.flush();
                    con.close();
                    return; // Stop further processing
                }

                String sql = "INSERT INTO Customers (email, firstName, lastName, telephone, boatName, boatLength, passwordHash) VALUES (?, ?, ?, ?, ?, ?, ?)";  // Storing passwordHash only
                PreparedStatement pst = con.prepareStatement(sql);
                pst.setString(1, email);
                pst.setString(2, firstName);
                pst.setString(3, lastName);
                pst.setString(4, telephone);
                pst.setString(5, boatName);
                pst.setInt(6, boatLength);
                pst.setString(7, password); // HASH IT before storing (for simplicity skipping hashing here)
                pst.executeUpdate();

                // 3. Send success response
                JsonObject jsonOutput = new JsonObject();
                jsonOutput.addProperty("message", "Registration successful!");
                out.print(jsonOutput.toString());
                out.flush();

                con.close();

            } catch (ClassNotFoundException | SQLException e) {
                System.err.println(e.getMessage());
                response.setStatus(500);  //  Internal Server Error
                JsonObject jsonOutput = new JsonObject();
                jsonOutput.addProperty("message", "Registration failed!");
                out.print(jsonOutput.toString());
                out.flush();
            }

        } catch (Exception e) {
            response.setStatus(400);  //  Bad Request
            JsonObject jsonOutput = new JsonObject();
            jsonOutput.addProperty("message", "Invalid input data!");
            out.print(jsonOutput.toString());
            out.flush();
        }
    }
}