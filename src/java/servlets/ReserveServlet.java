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

public class ReserveServlet extends HttpServlet {

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

            String boatLengthStr = jsonInput.get("boatLength").getAsString();
            int boatLength = Integer.parseInt(boatLengthStr);
            String checkInDate = jsonInput.get("checkInDate").getAsString();
            String email = jsonInput.get("email").getAsString(); //Retrieve user Email

            System.out.println("Received data: boatLength=" + boatLength + ", checkInDate=" + checkInDate + " email=" + email);

            // 2. Database logic
            try {
                Class.forName("com.mysql.cj.jdbc.Driver");
                Connection con = DriverManager.getConnection(DB_URL, DB_USER, DB_PASSWORD);

                //Get Boat Name from DB using the email
                String sqlGet = "SELECT boatName FROM Customers WHERE email = ?";
                 PreparedStatement pstGet = con.prepareStatement(sqlGet);
                 pstGet.setString(1, email);
                ResultSet rs = pstGet.executeQuery();
                String boatName;
                if (rs.next()) {
                    boatName = rs.getString("boatName");
                } else {
                    boatName = "Default name for user";
                     System.out.println("Error, couldn't find boat name for the user!  email=" + email);
                }

                // Determine the appropriate slip size
                int slipSize;
                if (boatLength <= 26) {
                    slipSize = 26;
                } else if (boatLength <= 40) {
                    slipSize = 40;
                } else {
                    slipSize = 50;
                }

                 // Check the availability of slips for the required slipSize
                 //TODO: implement availability
                 boolean slipAvailable = true; //For now available is true

                JsonObject jsonOutput = new JsonObject();
                 if(slipAvailable){
                      // 3. Send success response
                      String message = "A " + slipSize + " foot slip is available. Reservation successful! for Boat " + boatName;
                    jsonOutput.addProperty("message", message);
                    out.print(jsonOutput.toString());
                    out.flush();
                 } else {
                     String message = "A " + slipSize + " foot slip is NOT available. for Boat " + boatName;
                    jsonOutput.addProperty("message", message);
                    out.print(jsonOutput.toString());
                    out.flush();
                    response.setStatus(404); // Not found
                 }


                con.close();

            } catch (ClassNotFoundException | SQLException e) {
                System.err.println(e.getMessage());
                response.setStatus(500); // Internal Server Error
                JsonObject jsonOutput = new JsonObject();
                jsonOutput.addProperty("message", "Error checking availability: " + e.getMessage());
                out.print(jsonOutput.toString());
                out.flush();
            }

        } catch (Exception e) {
            System.err.println("Error reading JSON data: " + e.getMessage());
            response.setStatus(400); // Bad Request
            JsonObject jsonOutput = new JsonObject();
            jsonOutput.addProperty("message", "Invalid input data:  " + e.getMessage());
            out.print(jsonOutput.toString());
            out.flush();
        }
    }
}